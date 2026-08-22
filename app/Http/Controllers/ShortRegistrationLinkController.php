<?php

namespace App\Http\Controllers;

use App\Services\Deeplink\EventRegistrationLinkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortRegistrationLinkController extends Controller
{
    public function __invoke(string $code, Request $request, EventRegistrationLinkService $links): RedirectResponse
    {
        $link = $links->findUsableByCode($code);

        if (! $link) {
            throw new NotFoundHttpException('Link no encontrado o vencido.');
        }

        $links->recordVisit($link, $request);

        return redirect()->away($link->longActivateUrl(), 302);
    }
}
