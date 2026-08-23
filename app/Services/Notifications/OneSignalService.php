<?php

namespace App\Services\Notifications;

use App\Contracts\OneSignalServiceInterface;
use App\Models\OneSignalRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OneSignalService implements OneSignalServiceInterface
{
    private const URI = 'https://api.onesignal.com/notifications';

    public function sendToUsers(string $title, string $message, array $externalIds): OneSignalSendResult
    {
        return $this->post([
            'app_id' => $this->appId(),
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
            'include_aliases' => [
                'external_id' => array_values($externalIds),
            ],
            'target_channel' => 'push',
        ]);
    }

    public function sendToAll(string $title, string $message): OneSignalSendResult
    {
        return $this->post([
            'app_id' => $this->appId(),
            'headings' => ['en' => $title],
            'contents' => ['en' => $message],
            'included_segments' => ['Subscribed Users'],
            'target_channel' => 'push',
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function post(array $payload): OneSignalSendResult
    {
        $apiKey = $this->apiKey();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key '.$apiKey,
                'Accept' => 'application/json',
            ])->timeout(15)->post(self::URI, $payload);
        } catch (ConnectionException $e) {
            $this->log('POST', self::URI, $payload, ['message' => $e->getMessage()], 0);

            return new OneSignalSendResult(false, null, 0, ['message' => $e->getMessage()]);
        }

        $body = $this->decodeBody($response);
        $this->log('POST', self::URI, $payload, $body, $response->status());

        $externalId = $body['id'] ?? null;
        $externalId = is_string($externalId) && $externalId !== '' ? $externalId : null;
        $ok = $response->successful() && $externalId !== null;

        return new OneSignalSendResult($ok, $externalId, $response->status(), $body);
    }

    /**
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>|null  $response
     */
    private function log(string $method, string $uri, array $body, ?array $response, int $status): void
    {
        OneSignalRequest::query()->create([
            'method' => $method,
            'uri' => $uri,
            'body' => $body,
            'response' => $response,
            'status' => $status,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeBody(Response $response): ?array
    {
        $json = $response->json();

        return is_array($json) ? $json : ['raw' => $response->body()];
    }

    private function appId(): string
    {
        $appId = (string) config('services.onesignal.app_id');

        if ($appId === '') {
            throw new RuntimeException('ONESIGNAL_APP_ID no está configurado.');
        }

        return $appId;
    }

    private function apiKey(): string
    {
        $apiKey = (string) config('services.onesignal.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('ONESIGNAL_API_KEY no está configurado.');
        }

        return $apiKey;
    }
}
