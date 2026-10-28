<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $service): Response
    {
        $response = $next($request);

        try {
            ApiLog::query()->create([
                'service' => $service,
                'method' => $request->getMethod(),
                'endpoint' => '/'.$request->path(),
                'ip' => $request->ip(),
                'request_body' => $this->requestBody($request),
                'response_body' => $this->body($response->getContent()),
                'response_code' => $response->getStatusCode(),
            ]);
        } catch (\Throwable $e) {
            Log::error('api_log.failed', [
                'service' => $service,
                'message' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    private function requestBody(Request $request): ?string
    {
        $content = $request->getContent();

        if (is_string($content) && $content !== '') {
            return $content;
        }

        $payload = $request->except(array_keys($request->allFiles()));

        foreach ($request->allFiles() as $key => $file) {
            $payload[$key] = $this->fileMeta($file);
        }

        if ($payload === []) {
            return null;
        }

        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: null;
    }

    /**
     * @param  UploadedFile|array<int, UploadedFile>  $file
     * @return array<string, mixed>|list<array<string, mixed>>
     */
    private function fileMeta(UploadedFile|array $file): array
    {
        if (is_array($file)) {
            return array_map(fn (UploadedFile $item) => $this->singleFileMeta($item), $file);
        }

        return $this->singleFileMeta($file);
    }

    /**
     * @return array{name: string|null, mime: string|null, size: int|false}
     */
    private function singleFileMeta(UploadedFile $file): array
    {
        return [
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];
    }

    private function body(string|false $content): ?string
    {
        if (! is_string($content) || $content === '') {
            return null;
        }

        return $content;
    }
}
