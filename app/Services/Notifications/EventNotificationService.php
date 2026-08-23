<?php

namespace App\Services\Notifications;

use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use App\Jobs\SendEventNotificationJob;
use App\Models\EventNotification;
use Carbon\Carbon;

class EventNotificationService
{
    /**
     * @param  array{
     *     event_id: int,
     *     type: string,
     *     scope: string,
     *     title: string,
     *     message: string,
     *     send_at?: string|null,
     *     invitation_ids?: list<int>
     * }  $data
     */
    public function create(array $data): EventNotification
    {
        $type = NotificationType::from($data['type']);
        $scope = NotificationScope::from($data['scope']);
        $sendAt = $type === NotificationType::Scheduled
            ? Carbon::parse($data['send_at'])
            : null;

        $notification = EventNotification::query()->create([
            'event_id' => $data['event_id'],
            'type' => $type,
            'scope' => $scope,
            'status' => NotificationStatus::Pending,
            'title' => $data['title'],
            'message' => $data['message'],
            'send_at' => $sendAt,
        ]);

        if ($scope === NotificationScope::Specific) {
            $notification->invitations()->sync($data['invitation_ids'] ?? []);
        }

        $job = new SendEventNotificationJob($notification->id);

        if ($type === NotificationType::Scheduled && $sendAt) {
            dispatch($job)->delay($sendAt);
        } else {
            dispatch($job);
        }

        return $notification->load('invitations');
    }

    public function updatePending(EventNotification $notification, array $data): EventNotification
    {
        $notification->update([
            'title' => $data['title'],
            'message' => $data['message'],
        ]);

        return $notification->fresh()->load('invitations');
    }

    public function cancel(EventNotification $notification): void
    {
        if ($notification->status === NotificationStatus::Pending) {
            $notification->update(['status' => NotificationStatus::Cancelled]);
        }

        $notification->delete();
    }
}
