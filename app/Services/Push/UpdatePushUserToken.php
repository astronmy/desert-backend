<?php

namespace App\Services\Push;

use App\Models\MicroserviceLog;

class UpdatePushUserToken
{
    public function __construct(
        private readonly PushServiceClient $client,
    ) {}

    public function update(
        string $externalUserId,
        string $firebaseToken,
        ?string $platform = null,
        ?int $eventId = null,
        ?int $userId = null,
    ): PushServiceResult {
        $body = [
            'firebase_token' => $firebaseToken,
        ];

        if (filled($platform)) {
            $body['platform'] = $platform;
        }

        return $this->client->send(
            MicroserviceLog::SERVICE_UPDATE_TOKEN,
            'PUT',
            '/api/v1/users/'.rawurlencode($externalUserId).'/token',
            $body,
            $eventId,
            $userId,
        );
    }
}
