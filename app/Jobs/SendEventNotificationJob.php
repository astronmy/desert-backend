<?php

namespace App\Jobs;

use App\Contracts\OneSignalServiceInterface;
use App\Enums\NotificationScope;
use App\Enums\NotificationStatus;
use App\Models\EventNotification;
use App\Models\Invitation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class SendEventNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public readonly int $eventNotificationId
    ) {}

    public function handle(OneSignalServiceInterface $oneSignal): void
    {
        $notification = EventNotification::query()->find($this->eventNotificationId);

        if (! $notification || $notification->status !== NotificationStatus::Pending) {
            return;
        }

        $externalIds = $this->recipientUuids($notification);

        if ($externalIds === []) {
            $notification->update(['status' => NotificationStatus::Failed]);

            return;
        }

        try {
            $result = $oneSignal->sendToUsers(
                $notification->title,
                $notification->message,
                $externalIds
            );
        } catch (Throwable) {
            $notification->update(['status' => NotificationStatus::Failed]);

            return;
        }

        if ($result->ok) {
            $notification->update([
                'status' => NotificationStatus::Sent,
                'external_id' => $result->externalId,
                'sent_at' => now(),
            ]);

            return;
        }

        $notification->update(['status' => NotificationStatus::Failed]);
    }

    /**
     * @return list<string>
     */
    private function recipientUuids(EventNotification $notification): array
    {
        if ($notification->scope === NotificationScope::General) {
            return Invitation::query()
                ->where('event_id', $notification->event_id)
                ->whereNotNull('uuid_notification')
                ->pluck('uuid_notification')
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return $notification->invitations()
            ->whereNotNull('uuid_notification')
            ->pluck('uuid_notification')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
