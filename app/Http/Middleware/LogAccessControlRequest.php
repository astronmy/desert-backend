<?php

namespace App\Http\Middleware;

use App\Models\AccessControlLog;
use App\Models\Invitation;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogAccessControlRequest
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $service): Response
    {
        $response = $next($request);

        try {
            AccessControlLog::query()->create([
                'service' => $service,
                'method' => $request->getMethod(),
                'endpoint' => '/'.$request->path(),
                'request_body' => $this->body($request->getContent()),
                'response_body' => $this->body($response->getContent()),
                'response_code' => $response->getStatusCode(),
                'user_id' => $request->user()?->id,
                'event_id' => $this->eventId($request),
            ]);
        } catch (\Throwable $e) {
            Log::error('access_control_log.failed', [
                'service' => $service,
                'message' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    private function eventId(Request $request): ?int
    {
        $code = $request->route('code');

        if (! is_string($code) || trim($code) === '') {
            $payload = json_decode($request->getContent(), true);
            $code = is_array($payload) ? ($payload['code'] ?? null) : null;
        }

        if (! is_string($code) || trim($code) === '') {
            return null;
        }

        $eventId = Invitation::query()
            ->where('code', Str::upper(trim($code)))
            ->value('event_id');

        return $eventId !== null ? (int) $eventId : null;
    }

    private function body(string|false $content): ?string
    {
        if (! is_string($content) || $content === '') {
            return null;
        }

        return $content;
    }
}
