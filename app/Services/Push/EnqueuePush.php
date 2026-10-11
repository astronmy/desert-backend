<?php

namespace App\Services\Push;

use App\Models\MicroserviceLog;
use Carbon\CarbonInterface;

class EnqueuePush
{
    public function __construct(
        private readonly PushServiceClient $client,
    ) {}

    /**
     * @param  list<string>  $externalUserIds
     * @param  array<string, mixed>|null  $data
     */
    public function enqueue(
        array $externalUserIds,
        string $title,
        string $body,
        ?array $data = null,
        ?string $imageUrl = null,
        ?CarbonInterface $scheduledAt = null,
        ?int $eventId = null,
        ?int $userId = null,
    ): PushServiceResult {
        $payload = [
            'external_user_ids' => array_values($externalUserIds),
            'title' => $title,
            'body' => $body,
        ];

        if ($data !== null && $data !== []) {
            $payload['data'] = $data;
        }

        if (filled($imageUrl)) {
            $payload['image_url'] = $imageUrl;
        }

        if ($scheduledAt !== null) {
            $payload['scheduled_at'] = $scheduledAt->toIso8601String();
        }

        return $this->client->send(
            MicroserviceLog::SERVICE_ENQUEUE_PUSH,
            'POST',
            '/api/v1/pushes',
            $payload,
            $eventId,
            $userId,
        );
    }
}
