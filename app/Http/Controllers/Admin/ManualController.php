<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ManualController extends Controller
{
    /**
     * Serve the user manual HTML, gated behind the app's own auth middleware
     * (see routes/admin/manual.php) rather than a static, publicly reachable file.
     */
    public function show(): Response
    {
        $path = resource_path('manual/manual-desert.html');

        abort_unless(is_file($path), 404);

        $html = file_get_contents($path);

        // Relative asset paths (screenshots/…, manual-desert.pdf) in the source
        // file resolve correctly once served under /admin/manual/, thanks to
        // this injected <base> tag — no need to fork the file for the web route.
        $html = str_replace('<head>', '<head><base href="'.url('/admin/manual/').'/">', $html);

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function pdf(): BinaryFileResponse
    {
        $path = resource_path('manual/manual-desert.pdf');

        abort_unless(is_file($path), 404);

        return response()->download($path, 'manual-desert-eventos.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function screenshot(string $file): Response
    {
        // basename() strips any path traversal segments before we touch the disk.
        $safe = basename($file);

        abort_unless(str_ends_with($safe, '.png'), 404);

        $path = resource_path('manual/screenshots/'.$safe);

        abort_unless(is_file($path), 404);

        return response(file_get_contents($path))
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'private, max-age=86400');
    }
}
