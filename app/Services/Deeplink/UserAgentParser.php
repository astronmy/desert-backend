<?php

namespace App\Services\Deeplink;

/**
 * Lightweight UA parser — no external dependency.
 *
 * @phpstan-type ParsedUa array{
 *   device_type: 'mobile'|'tablet'|'desktop'|'bot'|'unknown',
 *   os: string|null,
 *   browser: string|null
 * }
 */
class UserAgentParser
{
    /**
     * @return ParsedUa
     */
    public function parse(?string $userAgent): array
    {
        if ($userAgent === null || trim($userAgent) === '') {
            return [
                'device_type' => 'unknown',
                'os' => null,
                'browser' => null,
            ];
        }

        $ua = $userAgent;

        if ($this->isBot($ua)) {
            return [
                'device_type' => 'bot',
                'os' => $this->detectOs($ua),
                'browser' => $this->detectBrowser($ua),
            ];
        }

        return [
            'device_type' => $this->detectDeviceType($ua),
            'os' => $this->detectOs($ua),
            'browser' => $this->detectBrowser($ua),
        ];
    }

    private function isBot(string $ua): bool
    {
        return (bool) preg_match(
            '/bot|crawl|spider|slurp|facebookexternalhit|preview|wget|curl|python-requests|headless/i',
            $ua
        );
    }

    /**
     * @return 'mobile'|'tablet'|'desktop'|'unknown'
     */
    private function detectDeviceType(string $ua): string
    {
        if (preg_match('/ipad|tablet|kindle|playbook|silk|(android(?!.*mobile))/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/mobi|iphone|ipod|android.*mobile|windows phone|blackberry|opera mini|opera mobi/i', $ua)) {
            return 'mobile';
        }

        if (preg_match('/windows|macintosh|linux|cros|x11/i', $ua)) {
            return 'desktop';
        }

        return 'unknown';
    }

    private function detectOs(string $ua): ?string
    {
        return match (true) {
            (bool) preg_match('/iphone|ipad|ipod/i', $ua) => 'ios',
            (bool) preg_match('/android/i', $ua) => 'android',
            (bool) preg_match('/windows/i', $ua) => 'windows',
            (bool) preg_match('/macintosh|mac os x/i', $ua) => 'macos',
            (bool) preg_match('/linux|cros/i', $ua) => 'other',
            default => null,
        };
    }

    private function detectBrowser(string $ua): ?string
    {
        return match (true) {
            (bool) preg_match('/edg\//i', $ua) => 'edge',
            (bool) preg_match('/opr\/|opera/i', $ua) => 'opera',
            (bool) preg_match('/chrome|crios/i', $ua) && ! preg_match('/edg\//i', $ua) => 'chrome',
            (bool) preg_match('/safari/i', $ua) && ! preg_match('/chrome|crios|android/i', $ua) => 'safari',
            (bool) preg_match('/firefox|fxios/i', $ua) => 'firefox',
            (bool) preg_match('/samsungbrowser/i', $ua) => 'samsung',
            default => null,
        };
    }
}
