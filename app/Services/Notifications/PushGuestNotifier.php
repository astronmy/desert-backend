<?php

namespace App\Services\Notifications;

use App\Contracts\GuestNotifier;
use App\Models\Invitation;
use App\Services\Push\EnqueuePush;

class PushGuestNotifier implements GuestNotifier
{
    public function __construct(
        private readonly EnqueuePush $pushes,
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

        $this->send(
            $invitation,
            __('access.push.welcome_title'),
            __('access.push.welcome_message', ['event' => $invitation->event?->name ?? '']),
        );
    }

    private function send(Invitation $invitation, string $title, string $message): void
    {
        if (! filled($invitation->firebase_token)) {
            return;
        }

        $this->pushes->enqueue(
            [(string) $invitation->id],
            $title,
            $message,
            eventId: $invitation->event_id,
            userId: auth()->id(),
        );
    }
}
