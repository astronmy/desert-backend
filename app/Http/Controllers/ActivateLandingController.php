<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivateLandingController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('activar.index', [
            'token' => $request->query('token'),
            'feature' => $request->query('feature') ?: config('services.deeplink.feature'),
            'code' => $request->query('code'),
            'playStoreUrl' => config('services.deeplink.play_store_url'),
            'appStoreUrl' => config('services.deeplink.app_store_url'),
            'appPackage' => 'ar.com.deserteventos.app',
            'deeplinkHost' => parse_url((string) config('services.deeplink.base_url'), PHP_URL_HOST) ?: 'desert.rxstudio.dev',
        ]);
    }
}
