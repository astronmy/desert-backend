<?php

namespace App\Services\Push;

use App\Models\MicroserviceLog;

class RegisterPushUser
{
    public function __construct(
        private readonly PushServiceClient $client,
    ) {}

    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function register(
        string $externalUserId,
        string $firebaseToken,
        string $platform,
        ?string $name = null,
        ?array $meta = null,
        ?int $eventId = null,
        ?int $userId = null,
    ): PushServiceResult {
        $body = [
            'external_user_id' => $externalUserId,
            'firebase_token' => $firebaseToken,
            'platform' => $platform,
        ];

        if (filled($name)) {
            $body['name'] = $name;
        }

        if ($meta !== null && $meta !== []) {
            $body['meta'] = $meta;
        }

        return $this->client->send(
            MicroserviceLog::SERVICE_REGISTER_USER,
            'POST',
            '/api/v1/users',
            $body,
            $eventId,
            $userId,
        );
    }
}
