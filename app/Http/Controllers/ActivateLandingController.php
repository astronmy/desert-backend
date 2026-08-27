<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivateLandingController extends Controller
{
    public function __invoke(Request $request): View
    {
        $token = $request->query('token');
        $feature = $request->query('feature') ?: config('services.deeplink.feature');
        $query = http_build_query(array_filter([
            'feature' => is_string($feature) ? $feature : null,
            'token' => is_string($token) ? $token : null,
        ], fn ($value) => $value !== null && $value !== ''));

        $host = parse_url((string) config('services.deeplink.base_url'), PHP_URL_HOST) ?: 'desert.rxstudio.dev';
        $package = 'ar.com.deserteventos.app';

        return view('activar.index', [
            'token' => $token,
            'feature' => $feature,
            'code' => $request->query('code'),
            'playStoreUrl' => config('services.deeplink.play_store_url'),
            'appStoreUrl' => config('services.deeplink.app_store_url'),
            'customSchemeUrl' => 'deserteventos://activar'.($query !== '' ? '?'.$query : ''),
            'intentUrl' => 'intent://activar'.($query !== '' ? '?'.$query : '').
                '#Intent;scheme=https;host='.$host.';package='.$package.';end',
        ]);
    }
}
