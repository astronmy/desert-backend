<?php

namespace App\Services\Deeplink;

use App\Models\Event;
use App\Models\EventRegistrationLink;
use App\Models\RegistrationLinkHit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventRegistrationLinkService
{
    private const SHORT_CODE_LENGTH = 8;

    private const SHORT_CODE_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function __construct(
        private readonly DeeplinkTokenService $tokens,
        private readonly UserAgentParser $userAgentParser,
    ) {}

    public function activeForEvent(Event $event): ?EventRegistrationLink
    {
        return EventRegistrationLink::query()
            ->where('event_id', $event->id)
            ->active()
            ->latest('id')
            ->first();
    }

    /**
     * Create or regenerate the active short registration link for an event.
     * Previous active links are revoked.
     */
    public function issueOrRegenerate(Event $event): EventRegistrationLink
    {
        return DB::transaction(function () use ($event) {
            EventRegistrationLink::query()
                ->where('event_id', $event->id)
                ->whereNull('revoked_at')
                ->update(['revoked_at' => now()]);

            $issued = $this->tokens->issue($event);

            return EventRegistrationLink::query()->create([
                'event_id' => $event->id,
                'short_code' => $this->generateUniqueShortCode(),
                'token' => $issued['token'],
                'jti' => $issued['jti'],
                'expires_at' => $issued['expires_at'],
                'revoked_at' => null,
            ]);
        });
    }

    public function findUsableByCode(string $code): ?EventRegistrationLink
    {
        $code = strtoupper(trim($code));

        $link = EventRegistrationLink::query()
            ->where('short_code', $code)
            ->first();

        if (! $link || ! $link->isUsable()) {
            return null;
        }

        return $link;
    }

    public function recordVisit(EventRegistrationLink $link, Request $request): RegistrationLinkHit
    {
        $parsed = $this->userAgentParser->parse($request->userAgent());
        $ua = $request->userAgent();

        return RegistrationLinkHit::query()->create([
            'event_registration_link_id' => $link->id,
            'event_id' => $link->event_id,
            'visited_at' => now(),
            'ip_hash' => $this->hashIp($request->ip()),
            'user_agent' => $ua !== null ? Str::limit($ua, 512, '') : null,
            'device_type' => $parsed['device_type'],
            'os' => $parsed['os'],
            'browser' => $parsed['browser'],
            'referrer' => $this->truncateReferrer($request->headers->get('referer')),
            'is_store_click' => false,
            'store' => null,
        ]);
    }

    /**
     * Record a store tap from the activate landing (Play / App Store).
     */
    public function recordStoreClick(
        EventRegistrationLink $link,
        Request $request,
        string $store,
    ): RegistrationLinkHit {
        $store = in_array($store, ['play', 'app_store'], true) ? $store : null;
        $parsed = $this->userAgentParser->parse($request->userAgent());
        $ua = $request->userAgent();

        return RegistrationLinkHit::query()->create([
            'event_registration_link_id' => $link->id,
            'event_id' => $link->event_id,
            'visited_at' => now(),
            'ip_hash' => $this->hashIp($request->ip()),
            'user_agent' => $ua !== null ? Str::limit($ua, 512, '') : null,
            'device_type' => $parsed['device_type'],
            'os' => $parsed['os'],
            'browser' => $parsed['browser'],
            'referrer' => $this->truncateReferrer($request->headers->get('referer')),
            'is_store_click' => true,
            'store' => $store,
        ]);
    }

    /**
     * Resolve link from short code or from the long token (landing may have either).
     */
    public function resolveFromCodeOrToken(?string $code, ?string $token): ?EventRegistrationLink
    {
        if ($code !== null && $code !== '') {
            return $this->findUsableByCode($code);
        }

        if ($token === null || $token === '') {
            return null;
        }

        return EventRegistrationLink::query()
            ->where('token', $token)
            ->whereNull('revoked_at')
            ->where('expires_at', '>=', now())
            ->latest('id')
            ->first();
    }

    /**
     * @return array{
     *   short_url: string|null,
     *   expires_at: string|null,
     *   expires_at_iso: string|null,
     *   has_link: bool,
     *   short_code: string|null
     * }
     */
    public function modalPayload(Event $event): array
    {
        $link = $this->activeForEvent($event);

        if (! $link) {
            return [
                'short_url' => null,
                'expires_at' => null,
                'expires_at_iso' => null,
                'has_link' => false,
                'short_code' => null,
            ];
        }

        return [
            'short_url' => $link->shortUrl(),
            'expires_at' => $link->expires_at->timezone(config('app.timezone'))->format('d/m/Y H:i'),
            'expires_at_iso' => $link->expires_at->toIso8601String(),
            'has_link' => true,
            'short_code' => $link->short_code,
        ];
    }

    private function generateUniqueShortCode(): string
    {
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $code = $this->randomShortCode();

            if (! EventRegistrationLink::query()->where('short_code', $code)->exists()) {
                return $code;
            }
        }

        throw new \RuntimeException('No se pudo generar un short_code único.');
    }

    private function randomShortCode(): string
    {
        $alphabet = self::SHORT_CODE_ALPHABET;
        $max = strlen($alphabet) - 1;
        $code = '';

        for ($i = 0; $i < self::SHORT_CODE_LENGTH; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return $code;
    }

    private function hashIp(?string $ip): string
    {
        $salt = (string) config('services.deeplink.secret', config('app.key'));

        return hash('sha256', ($ip ?? 'unknown').'|'.$salt);
    }

    private function truncateReferrer(?string $referrer): ?string
    {
        if ($referrer === null || $referrer === '') {
            return null;
        }

        return Str::limit($referrer, 512, '');
    }
}
