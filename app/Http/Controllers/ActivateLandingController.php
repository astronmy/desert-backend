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
            'feature' => $request->query('feature'),
            'playStoreUrl' => config('services.deeplink.play_store_url'),
            'appStoreUrl' => config('services.deeplink.app_store_url'),
        ]);
    }
}
