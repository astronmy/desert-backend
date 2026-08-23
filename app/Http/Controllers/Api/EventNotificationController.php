<?php

namespace App\Http\Controllers\Api;

use App\Enums\NotificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreEventNotificationRequest;
use App\Http\Requests\Api\UpdateEventNotificationRequest;
use App\Http\Resources\Api\EventNotificationResource;
use App\Models\Event;
use App\Models\EventNotification;
use App\Models\Invitation;
use App\Services\Notifications\EventNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventNotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $perPage = min(50, max(1, (int) $request->integer('per_page', 15)));

        $query = EventNotification::query()
            ->with('invitations:id')
            ->orderByDesc('id');

        $eventId = $request->integer('event_id');
        if ($eventId > 0) {
            abort_unless($user?->canAccessEvent($eventId), 404);
            $query->where('event_id', $eventId);
        } elseif ($user?->requiresEvent()) {
            $query->where('event_id', $user->event_id);
        }

        return EventNotificationResource::collection($query->paginate($perPage));
    }

    public function store(
        StoreEventNotificationRequest $request,
        EventNotificationService $notifications
    ): JsonResponse {
        $notification = $notifications->create($request->validated());

        return EventNotificationResource::make($notification)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, EventNotification $eventNotification): EventNotificationResource
    {
        abort_unless($request->user()?->canAccessEvent($eventNotification->event_id), 404);

        return EventNotificationResource::make($eventNotification->load('invitations:id'));
    }

    public function update(
        UpdateEventNotificationRequest $request,
        EventNotification $eventNotification,
        EventNotificationService $notifications
    ): EventNotificationResource|JsonResponse {
        if ($eventNotification->status !== NotificationStatus::Pending) {
            return response()->json([
                'message' => 'Solo se puede editar una notificación pendiente.',
            ], 409);
        }

        return EventNotificationResource::make(
            $notifications->updatePending($eventNotification, $request->validated())
        );
    }

    public function destroy(
        Request $request,
        EventNotification $eventNotification,
        EventNotificationService $notifications
    ): JsonResponse {
        abort_unless($request->user()?->canAccessEvent($eventNotification->event_id), 404);

        $notifications->cancel($eventNotification);

        return response()->json(null, 204);
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
                'uuid_notification' => $invitation->uuid_notification,
                'guest' => [
                    'first_name' => $invitation->guest->first_name,
                    'last_name' => $invitation->guest->last_name,
                ],
            ])->values(),
        ]);
    }
}
