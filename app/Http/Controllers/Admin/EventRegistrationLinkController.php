<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Deeplink\EventRegistrationLinkService;
use App\Services\Deeplink\RegistrationLinkMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class EventRegistrationLinkController extends Controller
{
    public function show(Event $event, EventRegistrationLinkService $links): JsonResponse
    {
        return response()->json($links->modalPayload($event));
    }

    public function store(Event $event, EventRegistrationLinkService $links): JsonResponse
    {
        $link = $links->issueOrRegenerate($event);

        return response()->json([
            'short_url' => $link->shortUrl(),
            'long_url' => $link->longActivateUrl(),
            'expires_at' => $link->expires_at->timezone(config('app.timezone'))->format('d/m/Y H:i'),
            'expires_at_iso' => $link->expires_at->toIso8601String(),
            'has_link' => true,
            'short_code' => $link->short_code,
            'message' => __('event.deeplink.generated'),
        ]);
    }

    public function metrics(Event $event, RegistrationLinkMetricsService $metrics): View
    {
        $data = $metrics->forEvent($event);

        return view('admin.events.link-metrics', [
            'event' => $event,
            ...$data,
        ]);
    }
}
