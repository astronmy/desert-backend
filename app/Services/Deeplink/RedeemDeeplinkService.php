<?php

namespace App\Services\Deeplink;

use App\Enums\InvitationStatus;
use App\Models\DeeplinkRedemption;
use App\Models\Invitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RedeemDeeplinkService
{
    public function __construct(
        private readonly DeeplinkTokenService $tokens
    ) {}

    /**
     * @return array{ok: true, feature: string, invitation_code: string, jti: string, expires_at: string}|array{ok: false, reason: string, message: string}
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

        $allowedFeature = (string) config('services.deeplink.feature', DeeplinkTokenService::FEATURE_INVITE);
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

        $invitation = Invitation::query()
            ->where('code', $payload['c'])
            ->first();

        if (! $invitation || $invitation->status === InvitationStatus::Cancelled) {
            return [
                'ok' => false,
                'reason' => 'invalid_signature',
                'message' => 'La invitación no es válida o está cancelada.',
            ];
        }

        return DB::transaction(function () use ($payload, $deviceId, $invitation) {
            $existing = DeeplinkRedemption::query()
                ->where('jti', $payload['jti'])
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->device_id !== $deviceId) {
                return [
                    'ok' => false,
                    'reason' => 'already_used',
                    'message' => 'Este link ya se activó en otro dispositivo.',
                ];
            }

            if (! $existing) {
                DeeplinkRedemption::create([
                    'jti' => $payload['jti'],
                    'device_id' => $deviceId,
                    'feature' => $payload['f'],
                    'invitation_code' => $invitation->code,
                    'redeemed_at' => now(),
                ]);
            }

            return [
                'ok' => true,
                'feature' => $payload['f'],
                'invitation_code' => $invitation->code,
                'jti' => $payload['jti'],
                'expires_at' => Carbon::createFromTimestamp($payload['exp'])->utc()->toIso8601String(),
            ];
        });
    }
}
