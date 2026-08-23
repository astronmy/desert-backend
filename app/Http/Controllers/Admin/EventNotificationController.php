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
        $events = $user?->requiresEvent()
            ? collect()
            : Event::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.notifications.index', compact('notifications', 'events'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $events = $user?->requiresEvent()
            ? Event::query()->whereKey($user->event_id)->get(['id', 'name'])
            : Event::query()->orderBy('name')->get(['id', 'name']);

        $lockedEventId = $user?->requiresEvent() ? $user->event_id : null;

        return view('admin.notifications.create', compact('events', 'lockedEventId'));
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
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $invitations->map(fn (Invitation $invitation) => [
                'id' => $invitation->id,
                'code' => $invitation->code,
                'guest' => trim($invitation->guest->first_name.' '.$invitation->guest->last_name),
            ])->values(),
        ]);
    }
}
