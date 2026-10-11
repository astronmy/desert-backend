<?php

namespace App\Services\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Invitation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class UpdateInvitationFirebaseTokenService
{
    public function __construct(
        private readonly SyncInvitationPushUser $syncInvitationPushUser,
    ) {}

    public function update(string $code, string $firebaseToken, string $platform, ?string $appVersion = null): Invitation
    {
        $invitation = Invitation::query()
            ->with('guest')
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

        $hadToken = filled($invitation->firebase_token);

        $invitation->update([
            'firebase_token' => $firebaseToken,
        ]);

        $this->syncInvitationPushUser->sync($invitation, $firebaseToken, $platform, $appVersion, $hadToken);

        return $invitation->refresh();
    }
}
