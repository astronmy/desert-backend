<?php

namespace App\Jobs;

use App\Enums\NotificationStatus;
use App\Models\EventNotification;
use App\Services\Push\GetPush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncPushStatusJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 8;

    public function __construct(
        public readonly int $eventNotificationId,
        public readonly ?int $userId = null,
    ) {}

    public function handle(GetPush $pushes): void
    {
        $notification = EventNotification::query()->find($this->eventNotificationId);

        if (
            ! $notification
            || $notification->status !== NotificationStatus::Pending
            || ! filled($notification->external_id)
        ) {
            return;
        }

        $result = $pushes->find(
            (string) $notification->external_id,
            $notification->event_id,
            $this->userId,
        );

        if (! $result->ok) {
            $this->retryLater();

            return;
        }

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

        if ($status === 'cancelled') {
            $notification->update(['status' => NotificationStatus::Cancelled]);

            return;
        }

        $this->retryLater();
    }

    private function retryLater(): void
    {
        if ($this->attempts() >= $this->tries || config('queue.default') === 'sync') {
            return;
        }

        $this->release(30);
    }
}
