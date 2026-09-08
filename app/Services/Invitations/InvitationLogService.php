<?php

namespace App\Services\Invitations;

use App\Enums\InvitationLogAction;
use App\Enums\InvitationStatus;
use App\Models\Invitation;
use App\Models\InvitationLog;

class InvitationLogService
{
    public function record(
        Invitation $invitation,
        InvitationLogAction $action,
        InvitationStatus $from,
        InvitationStatus $to,
        ?int $userId = null,
    ): void {
        InvitationLog::query()->create([
            'invitation_id' => $invitation->id,
            'event_id' => $invitation->event_id,
            'user_id' => $userId,
            'action' => $action,
            'status_from' => $from,
            'status_to' => $to,
            'created_at' => now(),
        ]);
    }
}
