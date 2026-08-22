<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentType;
use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invitation\GenerateDeeplinkRequest;
use App\Http\Requests\Admin\Invitation\ImportInvitationsRequest;
use App\Http\Requests\Admin\Invitation\StoreInvitationRequest;
use App\Http\Requests\Admin\Invitation\UpdateInvitationRequest;
use App\Models\DeeplinkRedemption;
use App\Models\Event;
use App\Models\Guest;
use App\Models\Invitation;
use App\Services\Deeplink\DeeplinkTokenService;
use App\Services\Invitations\ImportEventInvitationsService;
use App\Services\Invitations\InvitationCodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventInvitationController extends Controller
{
    public function index(Request $request, Event $event): View
    {
        $invitations = $event->invitations()
            ->with('guest')
            ->when($request->filled('name'), function ($q) use ($request) {
                $name = '%'.$request->string('name').'%';
                $q->whereHas('guest', fn ($g) => $g->where('first_name', 'like', $name)
                    ->orWhere('last_name', 'like', $name)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$name]));
            })
            ->when($request->filled('document_number'), function ($q) use ($request) {
                $q->whereHas('guest', fn ($g) => $g->where('document_number', 'like', '%'.$request->string('document_number').'%'));
            })
            ->when($request->filled('code'), fn ($q) => $q->where('code', 'like', '%'.$request->string('code').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statuses = InvitationStatus::options();

        return view('admin.events.invitations.index', compact('event', 'invitations', 'statuses'));
    }

    public function create(Event $event): View
    {
        $documentTypes = DocumentType::options();

        return view('admin.events.invitations.create', compact('event', 'documentTypes'));
    }

    public function store(
        StoreInvitationRequest $request,
        Event $event,
        InvitationCodeGenerator $codeGenerator
    ): RedirectResponse {
        $data = $request->validated();

        $guest = Guest::query()->updateOrCreate(
            [
                'id_type' => $data['id_type'],
                'document_number' => $data['document_number'],
            ],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
            ]
        );

        $exists = Invitation::query()
            ->where('event_id', $event->id)
            ->where('guest_id', $guest->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.events.invitations.index', $event)
                ->with('error', __('invitation.messages.already_exists'));
        }

        Invitation::create([
            'event_id' => $event->id,
            'guest_id' => $guest->id,
            'code' => $codeGenerator->generate(),
            'status' => InvitationStatus::Pending,
        ]);

        return redirect()
            ->route('admin.events.invitations.index', $event)
            ->with('status', __('invitation.messages.created'));
    }

    public function show(Event $event, Invitation $invitation): View
    {
        $this->ensureInvitationBelongsToEvent($event, $invitation);

        $invitation->load('guest');

        $redemptions = DeeplinkRedemption::query()
            ->where('invitation_code', $invitation->code)
            ->orderByDesc('redeemed_at')
            ->limit(20)
            ->get();

        return view('admin.events.invitations.show', compact('event', 'invitation', 'redemptions'));
    }

    public function generateDeeplink(
        GenerateDeeplinkRequest $request,
        Event $event,
        Invitation $invitation,
        DeeplinkTokenService $tokens
    ): RedirectResponse {
        $this->ensureInvitationBelongsToEvent($event, $invitation);

        if ($invitation->status === InvitationStatus::Cancelled) {
            return back()->with('error', __('invitation.deeplink.cancelled'));
        }

        $days = (int) ($request->validated('days')
            ?? config('services.deeplink.default_ttl_days', 30));

        $issued = $tokens->issue($invitation, now()->addDays($days));

        return redirect()
            ->route('admin.events.invitations.show', [$event, $invitation])
            ->with('status', __('invitation.deeplink.generated'))
            ->with('deeplink_url', $issued['url'])
            ->with('deeplink_expires_at', $issued['expires_at']->timezone(config('app.timezone'))->format('d/m/Y H:i'));
    }

    public function edit(Event $event, Invitation $invitation): View
    {
        $this->ensureInvitationBelongsToEvent($event, $invitation);

        $invitation->load('guest');
        $documentTypes = DocumentType::options();
        $statuses = InvitationStatus::options();

        return view('admin.events.invitations.edit', compact('event', 'invitation', 'documentTypes', 'statuses'));
    }

    public function update(UpdateInvitationRequest $request, Event $event, Invitation $invitation): RedirectResponse
    {
        $this->ensureInvitationBelongsToEvent($event, $invitation);

        $data = $request->validated();
        $guest = $invitation->guest;

        $conflict = Guest::query()
            ->where('id_type', $data['id_type'])
            ->where('document_number', $data['document_number'])
            ->where('id', '!=', $guest->id)
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors(['document_number' => __('invitation.messages.already_exists')]);
        }

        $guest->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'document_number' => $data['document_number'],
            'id_type' => $data['id_type'],
        ]);

        $status = InvitationStatus::from($data['status']);
        $invitation->update([
            'status' => $status,
            'confirmed_at' => $status === InvitationStatus::Confirmed
                ? ($invitation->confirmed_at ?? now())
                : null,
        ]);

        return redirect()
            ->route('admin.events.invitations.index', $event)
            ->with('status', __('invitation.messages.updated'));
    }

    public function destroy(Event $event, Invitation $invitation): RedirectResponse
    {
        $this->ensureInvitationBelongsToEvent($event, $invitation);

        $invitation->delete();

        return redirect()
            ->route('admin.events.invitations.index', $event)
            ->with('status', __('invitation.messages.deleted'));
    }

    public function importForm(Event $event): View
    {
        return view('admin.events.invitations.import', compact('event'));
    }

    public function importTemplate(Event $event): StreamedResponse
    {
        $filename = 'plantilla-invitaciones-'.$event->id.'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['Nombre', 'Apellido', 'DNI']);
            fputcsv($handle, ['Juan', 'Pérez', '30111222']);
            fputcsv($handle, ['María', 'García', '28999888']);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(
        ImportInvitationsRequest $request,
        Event $event,
        ImportEventInvitationsService $importService
    ): RedirectResponse {
        $summary = $importService->import($event, $request->file('file'));

        return redirect()
            ->route('admin.events.invitations.index', $event)
            ->with('status', __('invitation.import.summary', $summary));
    }

    private function ensureInvitationBelongsToEvent(Event $event, Invitation $invitation): void
    {
        abort_unless($invitation->event_id === $event->id, 404);
    }
}
