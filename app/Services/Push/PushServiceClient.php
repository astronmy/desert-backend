<?php

namespace App\Services\Push;

use App\Models\MicroserviceLog;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PushServiceClient
{
    /**
     * @param  array<string, mixed>|null  $body
     */
    public function send(
        string $service,
        string $method,
        string $path,
        ?array $body = null,
        ?int $eventId = null,
        ?int $userId = null,
    ): PushServiceResult {
        $method = strtoupper($method);
        $path = '/'.ltrim($path, '/');
        $baseUrl = rtrim((string) config('services.push.base_url'), '/');
        $apiKey = (string) config('services.push.api_key');

        if ($baseUrl === '' || $apiKey === '') {
            $payload = ['message' => 'PUSH_SERVICE_BASE_URL o PUSH_SERVICE_API_KEY no está configurado.'];
            $this->writeLog($service, $method, $path, $body, $payload, 0, $eventId, $userId);

            return new PushServiceResult(false, 0, $payload);
        }

        try {
            $pending = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout(15);

            $response = match ($method) {
                'GET' => $pending->get($baseUrl.$path),
                'PUT' => $pending->put($baseUrl.$path, $body ?? []),
                default => $pending->post($baseUrl.$path, $body ?? []),
            };
        } catch (ConnectionException $e) {
            $payload = ['message' => $e->getMessage()];
            $this->writeLog($service, $method, $path, $body, $payload, 0, $eventId, $userId);

            return new PushServiceResult(false, 0, $payload);
        }

        $decoded = $this->decodeBody($response);
        $this->writeLog($service, $method, $path, $body, $decoded, $response->status(), $eventId, $userId);

        $ok = $response->successful() && ($decoded['success'] ?? true) !== false;

        return new PushServiceResult($ok, $response->status(), $decoded);
    }

    /**
     * @param  array<string, mixed>|null  $requestBody
     * @param  array<string, mixed>|null  $responseBody
     */
    private function writeLog(
        string $service,
        string $method,
        string $endpoint,
        ?array $requestBody,
        ?array $responseBody,
        int $status,
        ?int $eventId,
        ?int $userId,
    ): void {
        try {
            MicroserviceLog::query()->create([
                'service' => $service,
                'method' => $method,
                'endpoint' => $endpoint,
                'request_body' => $this->encode($requestBody),
                'response_body' => $this->encode($responseBody),
                'response_code' => $status,
                'user_id' => $userId,
                'event_id' => $eventId,
            ]);
        } catch (Throwable $e) {
            Log::error('microservice_log.failed', [
                'service' => $service,
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function encode(?array $payload): ?string
    {
        if ($payload === null) {
            return null;
        }

        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $encoded === false ? null : $encoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeBody(Response $response): array
    {
        $json = $response->json();

        return is_array($json) ? $json : ['raw' => $response->body()];
    }
}
