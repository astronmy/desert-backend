<?php

namespace App\Services\Deeplink;

use App\Models\Invitation;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use RuntimeException;

class DeeplinkTokenService
{
    public const FEATURE_INVITE = 'invite';

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

    /**
     * @return array{token: string, url: string, jti: string, feature: string, invitation_code: string, expires_at: CarbonInterface}
     */
    public function issue(Invitation $invitation, CarbonInterface $expiresAt): array
    {
        $jti = (string) Str::uuid();
        $feature = (string) config('services.deeplink.feature', self::FEATURE_INVITE);

        $payloadJson = json_encode([
            'f' => $feature,
            'c' => $invitation->code,
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
            'invitation_code' => $invitation->code,
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * @return array{f: string, c: string, exp: int, jti: string}|null
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
        $code = $payload['c'] ?? null;
        $jti = $payload['jti'] ?? null;
        $exp = $payload['exp'] ?? null;

        if (! is_string($feature) || $feature === '') {
            return null;
        }
        if (! is_string($code) || $code === '') {
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
            'c' => Str::upper(trim($code)),
            'exp' => (int) $exp,
            'jti' => $jti,
        ];
    }

    public function b64url(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }
}
