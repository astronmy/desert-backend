<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventRegistrationLinkController;
use Illuminate\Support\Facades\Route;

Route::get('events/{event}/registration-link', [EventRegistrationLinkController::class, 'show'])
    ->name('events.registration-link.show');
Route::post('events/{event}/registration-link', [EventRegistrationLinkController::class, 'store'])
    ->name('events.registration-link.store');
Route::get('events/{event}/link-metrics', [EventRegistrationLinkController::class, 'metrics'])
    ->name('events.link-metrics');

// Legacy alias kept for old forms; delegates to short-link regenerate.
Route::post('events/{event}/deeplink', [EventController::class, 'generateDeeplink'])->name('events.deeplink');

Route::resource('events', EventController::class)->except(['show']);
