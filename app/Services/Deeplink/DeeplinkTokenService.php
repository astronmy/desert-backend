<?php

namespace App\Services\Deeplink;

use App\Models\Event;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use RuntimeException;

class DeeplinkTokenService
{
    public const FEATURE_EVENT_REGISTER = 'event_register';

    public function secret(): string
    {
        $secret = (string) config('services.deeplink.secret');

        if ($secret === '') {
            throw new RuntimeException('DEEPLINK_HMAC_SECRET no está configurado.');
        }

        return $secret;
    }

    public function baseUrl(): string
    {
        return rtrim((string) config('services.deeplink.base_url', 'https://desert.rxstudio.dev'), '/');
    }

    public function feature(): string
    {
        return (string) config('services.deeplink.feature', self::FEATURE_EVENT_REGISTER);
    }

    /**
     * Expiración del link = fin del día de end_date del evento (timezone de la app).
     */
    public function expiresAtForEvent(Event $event): CarbonInterface
    {
        return Carbon::parse($event->end_date->format('Y-m-d'), config('app.timezone'))
            ->endOfDay();
    }

    /**
     * @return array{token: string, url: string, jti: string, feature: string, event_id: int, expires_at: CarbonInterface}
     */
    public function issue(Event $event): array
    {
        $jti = (string) Str::uuid();
        $feature = $this->feature();
        $expiresAt = $this->expiresAtForEvent($event);

        $payloadJson = json_encode([
            'f' => $feature,
            'e' => $event->id,
            'exp' => $expiresAt->getTimestamp(),
            'jti' => $jti,
        ], JSON_THROW_ON_ERROR);

        $payloadB64 = $this->b64url($payloadJson);
        $signature = $this->b64url(hash_hmac('sha256', $payloadB64, $this->secret(), true));
        $token = "v1.{$payloadB64}.{$signature}";

        $url = $this->baseUrl().'/activar?feature='.rawurlencode($feature).'&token='.rawurlencode($token);

        return [
            'token' => $token,
            'url' => $url,
            'jti' => $jti,
            'feature' => $feature,
            'event_id' => $event->id,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * @return array{f: string, e: int, exp: int, jti: string}|null
     */
    public function verify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3 || $parts[0] !== 'v1') {
            return null;
        }

        [, $payloadB64, $signatureB64] = $parts;

        try {
            $expected = $this->b64url(hash_hmac('sha256', $payloadB64, $this->secret(), true));
        } catch (RuntimeException) {
            return null;
        }

        if (! hash_equals($expected, $signatureB64)) {
            return null;
        }

        $json = base64_decode(strtr($payloadB64, '-_', '+/'), true);
        if ($json === false) {
            return null;
        }

        try {
            $payload = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return null;
        }

        if (! is_array($payload)) {
            return null;
        }

        $feature = $payload['f'] ?? null;
        $eventId = $payload['e'] ?? null;
        $jti = $payload['jti'] ?? null;
        $exp = $payload['exp'] ?? null;

        if (! is_string($feature) || $feature === '') {
            return null;
        }
        if (! is_numeric($eventId)) {
            return null;
        }
        if (! is_string($jti) || $jti === '') {
            return null;
        }
        if (! is_numeric($exp)) {
            return null;
        }

        return [
            'f' => $feature,
            'e' => (int) $eventId,
            'exp' => (int) $exp,
            'jti' => $jti,
        ];
    }

    public function b64url(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
