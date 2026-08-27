<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Deeplink\EventRegistrationLinkService;
use App\Services\Deeplink\RedeemDeeplinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeeplinkController extends Controller
{
    public function redeem(Request $request, RedeemDeeplinkService $redeem): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'max:1024'],
            'device_id' => ['required', 'string', 'max:64'],
        ]);

        $result = $redeem->redeem($data['token'], $data['device_id']);

        if (! $result['ok']) {
            return response()->json([
                'valid' => false,
                'reason' => $result['reason'],
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'valid' => true,
            'feature' => $result['feature'],
            'event_id' => $result['event_id'],
            'jti' => $result['jti'],
            'expires_at' => $result['expires_at'],
        ]);
    }

    public function resolveShort(string $code, EventRegistrationLinkService $links): JsonResponse
    {
        $link = $links->findUsableByCode($code);

        if (! $link) {
            return response()->json([
                'valid' => false,
                'reason' => 'not_found',
                'message' => 'Link no encontrado o vencido.',
            ], 404);
        }

        return response()->json([
            'valid' => true,
            'token' => $link->token,
            'feature' => config('services.deeplink.feature'),
            'event_id' => $link->event_id,
            'expires_at' => $link->expires_at->toIso8601String(),
        ]);
    }
}
