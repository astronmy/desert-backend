<?php

namespace App\Http\Controllers;

use App\Services\Deeplink\EventRegistrationLinkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivateStoreClickController extends Controller
{
    public function __invoke(Request $request, EventRegistrationLinkService $links): JsonResponse
    {
        $validated = $request->validate([
            'store' => ['required', 'in:play,app_store'],
            'code' => ['nullable', 'string', 'max:16'],
            'token' => ['nullable', 'string', 'max:4096'],
        ]);

        $link = $links->resolveFromCodeOrToken(
            $validated['code'] ?? null,
            $validated['token'] ?? null,
        );

        if (! $link) {
            return response()->json(['ok' => false], 404);
        }

        $links->recordStoreClick($link, $request, $validated['store']);

        return response()->json(['ok' => true]);
    }
}
