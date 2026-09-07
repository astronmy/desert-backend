<?php

namespace App\Services\Notifications;

use App\Contracts\GuestNotifier;
use App\Contracts\OneSignalServiceInterface;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;
use Throwable;

class OneSignalGuestNotifier implements GuestNotifier
{
    public function __construct(
        private readonly OneSignalServiceInterface $oneSignal
    ) {}

    public function invitationApproved(Invitation $invitation): void
    {
        $invitation->loadMissing('event');

        $this->send(
            $invitation,
            __('invitation.push.approved_title'),
            __('invitation.push.approved_message', [
                'event' => $invitation->event?->name ?? '',
            ]),
        );
    }

    public function welcomeToEvent(Invitation $invitation): void
    {
        $invitation->loadMissing('event');
        $eventName = $invitation->event?->name ?? '';

        $this->send(
            $invitation,
            __('access.push.welcome_title'),
            __('access.push.welcome_message', ['event' => $eventName]),
        );
    }

    private function send(Invitation $invitation, string $title, string $message): void
    {
        $uuid = $invitation->uuid_notification;

        if (! is_string($uuid) || $uuid === '') {
            return;
        }

        try {
            $result = $this->oneSignal->sendToUsers($title, $message, [$uuid]);
        } catch (Throwable $e) {
            Log::warning('onesignal.guest_push_failed', [
                'invitation_id' => $invitation->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        if (! $result->ok) {
            Log::warning('onesignal.guest_push_rejected', [
                'invitation_id' => $invitation->id,
                'http_status' => $result->httpStatus,
            ]);
        }
    }
}
