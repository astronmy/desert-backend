<?php

use App\Http\Controllers\ActivateLandingController;
use App\Http\Controllers\ActivateStoreClickController;
use App\Http\Controllers\ShortRegistrationLinkController;
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
Route::post('/activar/store-click', ActivateStoreClickController::class)->name('activar.store-click');
Route::get('/r/{code}', ShortRegistrationLinkController::class)
    ->where('code', '[A-Za-z0-9]{8}')
    ->name('registration.short');

Route::view('/terminos', 'legal.terminos')->name('legal.terminos');
Route::view('/privacidad', 'legal.privacidad')->name('legal.privacidad');

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
