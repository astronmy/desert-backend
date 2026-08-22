<?php

namespace App\Services\Notifications;

use App\Contracts\GuestNotifier;
use App\Models\Invitation;
use Illuminate\Support\Facades\Log;

class LogGuestNotifier implements GuestNotifier
{
    public function invitationApproved(Invitation $invitation): void
    {
        Log::info('onesignal.stub.invitation_approved', [
            'invitation_id' => $invitation->id,
            'code' => $invitation->code,
            'event_id' => $invitation->event_id,
            'guest_id' => $invitation->guest_id,
            'message' => 'Hemos confirmado tu invitación',
        ]);
    }
}
