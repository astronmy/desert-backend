<?php

namespace App\Jobs;

use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Models\EventNotification;
use App\Models\Invitation;
use App\Services\Push\EnqueuePush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendEventNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public readonly int $eventNotificationId,
        public readonly ?int $userId = null,
    ) {}

    public function handle(EnqueuePush $pushes): void
    {
        $notification = EventNotification::query()->find($this->eventNotificationId);

        if (! $notification || $notification->status !== NotificationStatus::Pending) {
            return;
        }

        $externalUserIds = $this->recipientIds($notification);

        if ($externalUserIds === []) {
            $notification->update(['status' => NotificationStatus::Failed]);

            return;
        }

        $scheduledAt = $notification->send_at?->isFuture() ? $notification->send_at : null;

        $result = $pushes->enqueue(
            $externalUserIds,
            $notification->title,
            $notification->message,
            scheduledAt: $scheduledAt,
            eventId: $notification->event_id,
            userId: $this->userId,
        );

        $pushId = $result->data()['id'] ?? null;

        if (! $result->ok || $pushId === null || $pushId === '') {
            $notification->update(['status' => NotificationStatus::Failed]);

            return;
        }

        $notification->update([
            'external_id' => (string) $pushId,
        ]);

        $status = $result->data()['status'] ?? null;

        if ($status === 'sent') {
            $notification->update([
                'status' => NotificationStatus::Sent,
                'sent_at' => now(),
            ]);

            return;
        }

        if ($status === 'failed') {
            $notification->update(['status' => NotificationStatus::Failed]);

            return;
        }

        SyncPushStatusJob::dispatch($notification->id, $this->userId);
    }

    /**
     * @return list<string>
     */
    private function recipientIds(EventNotification $notification): array
    {
        $query = $notification->scope === NotificationScope::General
            ? Invitation::query()->where('event_id', $notification->event_id)
            : $notification->invitations();

        return $query
            ->whereNotNull('firebase_token')
            ->where('firebase_token', '!=', '')
            ->pluck($notification->scope === NotificationScope::General ? 'id' : 'invitations.id')
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values()
            ->all();
    }
}
