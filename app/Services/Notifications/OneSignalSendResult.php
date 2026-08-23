<?php

namespace App\Services\Notifications;

class OneSignalSendResult
{
    /**
     * @param  array<string, mixed>|null  $body
     */
    public function __construct(
        public readonly bool $ok,
        public readonly ?string $externalId,
        public readonly int $httpStatus,
        public readonly ?array $body,
    ) {}
}
