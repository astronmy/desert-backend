<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\EventRegistrationLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware(['permission:deeplink.ver', 'event.access'])->group(function () {
    Route::get('events/{event}/registration-link', [EventRegistrationLinkController::class, 'show'])
        ->name('events.registration-link.show');
    Route::get('events/{event}/link-metrics', [EventRegistrationLinkController::class, 'metrics'])
        ->name('events.link-metrics');
});

Route::middleware(['permission:deeplink.generar', 'event.access'])->group(function () {
    Route::post('events/{event}/registration-link', [EventRegistrationLinkController::class, 'store'])
        ->name('events.registration-link.store');
    Route::post('events/{event}/deeplink', [EventController::class, 'generateDeeplink'])->name('events.deeplink');
});

Route::middleware('permission:eventos.ver')->group(function () {
    Route::get('events', [EventController::class, 'index'])->name('events.index');
});

Route::middleware('permission:eventos.crear')->group(function () {
    Route::get('events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('events', [EventController::class, 'store'])->name('events.store');
});

Route::middleware('permission:eventos.editar')->group(function () {
    Route::get('events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('events/{event}', [EventController::class, 'update'])->name('events.update');
});

Route::middleware('permission:eventos.eliminar')->group(function () {
    Route::delete('events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
});
