<?php

use App\Http\Controllers\ActivateLandingController;
use App\Http\Controllers\WellKnownController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('login');
});

Route::get('/.well-known/assetlinks.json', [WellKnownController::class, 'assetLinks']);
Route::get('/.well-known/apple-app-site-association', [WellKnownController::class, 'appleAppSiteAssociation']);
Route::get('/activar', ActivateLandingController::class)->name('activar');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
