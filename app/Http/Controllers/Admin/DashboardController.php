<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Invitation;
use App\Services\Deeplink\EventRegistrationLinkService;
use App\Services\Invitations\ModerateInvitationsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, EventRegistrationLinkService $links): View
    {
        $user = $request->user();
        $user?->loadMissing(['role.permissions', 'event']);

        $event = null;
        $invitations = null;
        $registrationLink = null;
        $statuses = InvitationStatus::options();

        if ($user && $user->requiresEvent() && $user->event_id) {
            $event = Event::query()->find($user->event_id);

            if ($event) {
                $registrationLink = $links->activeForEvent($event);

                if ($user->canPermission('invitaciones.ver')) {
                    $invitations = $event->invitations()
                        ->with('guest')
                        ->when($request->filled('name'), function ($q) use ($request) {
                            $name = '%'.$request->string('name').'%';
                            $q->whereHas('guest', fn ($g) => $g->where('first_name', 'like', $name)
                                ->orWhere('last_name', 'like', $name)
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$name]));
                        })
                        ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
                        ->latest()
                        ->paginate(15)
                        ->withQueryString();
                }
            }
        }

        return view('dashboard', compact('event', 'invitations', 'statuses', 'registrationLink'));
    }

    public function generateLink(Request $request, EventRegistrationLinkService $links): RedirectResponse
    {
        $event = $this->clientEvent($request);

        $links->issueOrRegenerate($event);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', __('dashboard.link.generated'));
    }

    public function approve(
        Request $request,
        Invitation $invitation,
        ModerateInvitationsService $moderator
    ): RedirectResponse {
        $event = $this->clientEvent($request);
        abort_unless($invitation->event_id === $event->id, 404);

        $moderator->approve($event, [$invitation->id]);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', __('invitation.messages.approved'));
    }

    public function bulkApprove(Request $request, ModerateInvitationsService $moderator): RedirectResponse
    {
        $event = $this->clientEvent($request);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $result = $moderator->approve($event, $data['ids']);

        return redirect()
            ->route('admin.dashboard')
            ->with('status', __('invitation.messages.bulk_approved', ['count' => $result['updated']]));
    }

    private function clientEvent(Request $request): Event
    {
        $user = $request->user();
        abort_unless($user && $user->requiresEvent() && $user->event_id, 403);

        $event = Event::query()->find($user->event_id);
        abort_unless($event, 404);

        return $event;
    }
}
