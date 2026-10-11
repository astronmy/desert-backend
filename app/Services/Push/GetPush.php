<?php

namespace App\Services\Push;

use App\Models\MicroserviceLog;

class GetPush
{
    public function __construct(
        private readonly PushServiceClient $client,
    ) {}

    public function find(string $id, ?int $eventId = null, ?int $userId = null): PushServiceResult
    {
        return $this->client->send(
            MicroserviceLog::SERVICE_GET_PUSH,
            'GET',
            '/api/v1/pushes/'.rawurlencode($id),
            null,
            $eventId,
            $userId,
        );
    }
}
