<?php

namespace App\Http\Middleware;

use App\Models\AccessControlLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            ]);
        } catch (\Throwable $e) {
            Log::error('access_control_log.failed', [
                'service' => $service,
                'message' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    private function body(string|false $content): ?string
    {
        if (! is_string($content) || $content === '') {
            return null;
        }

        return $content;
    }
}
