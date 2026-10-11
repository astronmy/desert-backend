<?php

namespace App\Services\Invitations;

use App\Models\Invitation;
use App\Services\Push\RegisterPushUser;
use App\Services\Push\UpdatePushUserToken;

class SyncInvitationPushUser
{
    public function __construct(
        private readonly RegisterPushUser $registerPushUser,
        private readonly UpdatePushUserToken $updatePushUserToken,
    ) {}

    public function sync(
        Invitation $invitation,
        string $firebaseToken,
        ?string $platform,
        ?string $appVersion = null,
        bool $hadToken = false,
    ): void {
        $platform = is_string($platform) ? strtolower(trim($platform)) : '';

        if (! in_array($platform, ['ios', 'android'], true)) {
            return;
        }

        $invitation->loadMissing('guest');

        $externalUserId = (string) $invitation->id;
        $name = $invitation->guest?->fullName();
        $version = is_string($appVersion) ? trim($appVersion) : '';
        $meta = $version !== '' ? ['app_version' => $version] : null;

        if (! $hadToken) {
            $this->registerPushUser->register(
                $externalUserId,
                $firebaseToken,
                $platform,
                $name,
                $meta,
                $invitation->event_id,
            );

            return;
        }

        $updated = $this->updatePushUserToken->update(
            $externalUserId,
            $firebaseToken,
            $platform,
            $invitation->event_id,
        );

        if ($updated->notFound()) {
            $this->registerPushUser->register(
                $externalUserId,
                $firebaseToken,
                $platform,
                $name,
                $meta,
                $invitation->event_id,
            );
        }
    }
}
