<?php

use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\Route;

Route::post('events/{event}/deeplink', [EventController::class, 'generateDeeplink'])->name('events.deeplink');
Route::resource('events', EventController::class)->except(['show']);
