<?php

namespace App\Services\Push;

class PushServiceResult
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function __construct(
        public readonly bool $ok,
        public readonly int $status,
        public readonly array $body,
    ) {}

    public function notFound(): bool
    {
        return $this->status === 404;
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        $data = $this->body['data'] ?? [];

        return is_array($data) ? $data : [];
    }
}
