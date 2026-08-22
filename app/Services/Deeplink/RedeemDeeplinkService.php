<?php

namespace App\Services\Deeplink;

use App\Models\Event;
use Carbon\Carbon;

class RedeemDeeplinkService
{
    public function __construct(
        private readonly DeeplinkTokenService $tokens
    ) {}

    /**
     * Token de evento reutilizable: valida firma/exp/evento, no quema jti.
     *
     * @return array{ok: true, feature: string, event_id: int, jti: string, expires_at: string}|array{ok: false, reason: string, message: string}
     */
    public function redeem(string $token, string $deviceId): array
    {
        $payload = $this->tokens->verify($token);

        if ($payload === null) {
            return [
                'ok' => false,
                'reason' => 'invalid_signature',
                'message' => 'No pudimos verificar este link.',
            ];
        }

        $allowedFeature = $this->tokens->feature();
        if ($payload['f'] !== $allowedFeature) {
            return [
                'ok' => false,
                'reason' => 'unknown_feature',
                'message' => 'Esta versión no reconoce el beneficio del link.',
            ];
        }

        if ($payload['exp'] < now()->getTimestamp()) {
            $expiresAt = Carbon::createFromTimestamp($payload['exp']);

            return [
                'ok' => false,
                'reason' => 'expired',
                'message' => 'El link venció el '.$expiresAt->timezone(config('app.timezone'))->format('d/m/Y'),
            ];
        }

        $event = Event::query()->find($payload['e']);
        if (! $event) {
            return [
                'ok' => false,
                'reason' => 'invalid_signature',
                'message' => 'El evento del link no existe.',
            ];
        }

        return [
            'ok' => true,
            'feature' => $payload['f'],
            'event_id' => $event->id,
            'jti' => $payload['jti'],
            'expires_at' => Carbon::createFromTimestamp($payload['exp'])->utc()->toIso8601String(),
        ];
    }
}
