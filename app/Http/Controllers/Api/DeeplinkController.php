<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            'invitation_code' => $result['invitation_code'],
            'jti' => $result['jti'],
            'expires_at' => $result['expires_at'],
        ]);
    }
}
