<?php

use App\Http\Controllers\Admin\EventAccessController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:accesos.ver', 'event.access'])->group(function () {
    Route::get('events/{event}/accesses', [EventAccessController::class, 'index'])
        ->name('events.accesses.index');
});
