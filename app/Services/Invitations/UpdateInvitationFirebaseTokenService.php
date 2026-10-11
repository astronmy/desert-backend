<?php

namespace App\Services\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class UpdateInvitationFirebaseTokenService
{
    public function update(string $code, string $firebaseToken): Invitation
    {
        $invitation = Invitation::query()
            ->where('code', Str::upper(trim($code)))
            ->first();

        if (! $invitation) {
            throw new HttpResponseException(response()->json([
                'message' => 'Invitación no encontrada.',
            ], 404));
        }

        if ($invitation->status === InvitationStatus::Cancelled) {
            throw new HttpResponseException(response()->json([
                'message' => 'La invitación está cancelada.',
            ], 410));
        }

        $invitation->update([
            'firebase_token' => $firebaseToken,
        ]);

        return $invitation->refresh();
    }
}
