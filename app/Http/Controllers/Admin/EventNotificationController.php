<?php

namespace App\Http\Controllers\Admin;

use App\Enums\NotificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEventNotificationRequest;
use App\Http\Requests\Api\UpdateEventNotificationRequest;
use App\Models\Event;
use App\Models\EventNotification;
use App\Models\Invitation;
use App\Services\Notifications\EventNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventNotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = EventNotification::query()
            ->with('event')
            ->orderByDesc('id');

        $eventId = $request->integer('event_id');
        if ($user?->requiresEvent()) {
            $query->where('event_id', $user->event_id);
        } elseif ($eventId > 0) {
            abort_unless($user?->canAccessEvent($eventId), 404);
            $query->where('event_id', $eventId);
        }

        $notifications = $query->paginate(15)->withQueryString();
        $eventsForSelect = $user?->requiresEvent()
            ? []
            : $this->eventsForSelect();

        return view('admin.notifications.index', compact('notifications', 'eventsForSelect'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $lockedEventId = $user?->requiresEvent() ? $user->event_id : null;
        $eventsForSelect = $user?->requiresEvent()
            ? $this->eventsForSelect(Event::query()->whereKey($user->event_id))
            : $this->eventsForSelect();
        $lockedEventName = $user?->requiresEvent()
            ? ($eventsForSelect[0]['name'] ?? null)
            : null;

        return view('admin.notifications.create', compact('eventsForSelect', 'lockedEventId', 'lockedEventName'));
    }

    public function store(
        StoreEventNotificationRequest $request,
        EventNotificationService $notifications
    ): RedirectResponse {
        $notification = $notifications->create($request->validated());

        return redirect()
            ->route('admin.notifications.show', $notification)
            ->with('status', __('notification.messages.created'));
    }

    public function show(Request $request, EventNotification $eventNotification): View
    {
        abort_unless($request->user()?->canAccessEvent($eventNotification->event_id), 404);

        $eventNotification->load(['event', 'invitations.guest']);

        return view('admin.notifications.show', [
            'notification' => $eventNotification,
        ]);
    }

    public function edit(Request $request, EventNotification $eventNotification): View|RedirectResponse
    {
        abort_unless($request->user()?->canAccessEvent($eventNotification->event_id), 404);

        if ($eventNotification->status !== NotificationStatus::Pending) {
            return redirect()
                ->route('admin.notifications.show', $eventNotification)
                ->with('error', __('notification.messages.only_pending'));
        }

        return view('admin.notifications.edit', [
            'notification' => $eventNotification,
        ]);
    }

    public function update(
        UpdateEventNotificationRequest $request,
        EventNotification $eventNotification,
        EventNotificationService $notifications
    ): RedirectResponse {
        if ($eventNotification->status !== NotificationStatus::Pending) {
            return redirect()
                ->route('admin.notifications.show', $eventNotification)
                ->with('error', __('notification.messages.only_pending'));
        }

        $notifications->updatePending($eventNotification, $request->validated());

        return redirect()
            ->route('admin.notifications.show', $eventNotification)
            ->with('status', __('notification.messages.updated'));
    }

    public function destroy(
        Request $request,
        EventNotification $eventNotification,
        EventNotificationService $notifications
    ): RedirectResponse {
        abort_unless($request->user()?->canAccessEvent($eventNotification->event_id), 404);

        $notifications->cancel($eventNotification);

        return redirect()
            ->route('admin.notifications.index')
            ->with('status', __('notification.messages.deleted'));
    }

    public function notifiableInvitations(Request $request, Event $event): JsonResponse
    {
        abort_unless($request->user()?->canAccessEvent($event), 404);

        $invitations = Invitation::query()
            ->with('guest')
            ->where('event_id', $event->id)
            ->whereNotNull('uuid_notification')
            ->where('uuid_notification', '!=', '')
            ->orderBy('id')
            ->get()
            ->sortBy(fn (Invitation $invitation) => mb_strtolower($invitation->guest?->fullName() ?? ''))
            ->values();

        return response()->json([
            'data' => $invitations->map(fn (Invitation $invitation) => [
                'id' => $invitation->id,
                'code' => $invitation->code,
                'guest' => $invitation->guest?->fullName() ?? '',
                'document' => trim(($invitation->guest?->id_type?->label() ?? '').' '.($invitation->guest?->document_number ?? '')),
                'uuid' => $invitation->uuid_notification,
            ])->values(),
        ]);
    }

    /**
     * @return list<array{id: int, name: string, dates: string, type_label: string}>
     */
    private function eventsForSelect(?Builder $query = null): array
    {
        return ($query ?? Event::query())
            ->orderByDesc('init_date')
            ->orderBy('name')
            ->get(['id', 'name', 'init_date', 'end_date', 'type'])
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'name' => $event->name,
                'dates' => $event->init_date->format('d/m/Y').' – '.$event->end_date->format('d/m/Y'),
                'type_label' => $event->type->label(),
            ])
            ->values()
            ->all();
    }
}
