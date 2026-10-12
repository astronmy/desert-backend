<?php

namespace App\Services\Notifications;

use App\Contracts\GuestNotifier;
use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class LogGuestNotifier implements GuestNotifier
{
    public function registrationReceived(Invitation $invitation): void
    {
        if (! Setting::current()->texto_registro) {
            return;
        }

        Log::info('onesignal.stub.registration_received', [
            'invitation_id' => $invitation->id,
            'code' => $invitation->code,
            'event_id' => $invitation->event_id,
        ]);
    }

    public function invitationApproved(Invitation $invitation): void
    {
        if (! Setting::current()->texto_confirmacion) {
            return;
        }

        Log::info('onesignal.stub.invitation_approved', [
            'invitation_id' => $invitation->id,
            'code' => $invitation->code,
            'event_id' => $invitation->event_id,
            'guest_id' => $invitation->guest_id,
        ]);
    }

    public function welcomeToEvent(Invitation $invitation): void
    {
        Log::info('onesignal.stub.welcome_to_event', [
            'invitation_id' => $invitation->id,
            'code' => $invitation->code,
            'event_id' => $invitation->event_id,
        ]);
    }
}
