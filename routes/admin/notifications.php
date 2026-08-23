<?php

use App\Http\Controllers\Admin\EventNotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:notificaciones.ver')->group(function () {
    Route::get('notifications', [EventNotificationController::class, 'index'])
        ->name('notifications.index');
    Route::get('events/{event}/notifiable-invitations', [EventNotificationController::class, 'notifiableInvitations'])
        ->middleware('event.access')
        ->name('events.notifiable-invitations');
});

Route::middleware('permission:notificaciones.crear')->group(function () {
    Route::get('notifications/create', [EventNotificationController::class, 'create'])
        ->name('notifications.create');
    Route::post('notifications', [EventNotificationController::class, 'store'])
        ->name('notifications.store');
});

Route::middleware('permission:notificaciones.ver')->group(function () {
    Route::get('notifications/{event_notification}', [EventNotificationController::class, 'show'])
        ->name('notifications.show');
});

Route::middleware('permission:notificaciones.editar')->group(function () {
    Route::get('notifications/{event_notification}/edit', [EventNotificationController::class, 'edit'])
        ->name('notifications.edit');
    Route::put('notifications/{event_notification}', [EventNotificationController::class, 'update'])
        ->name('notifications.update');
});

Route::middleware('permission:notificaciones.eliminar')->group(function () {
    Route::delete('notifications/{event_notification}', [EventNotificationController::class, 'destroy'])
        ->name('notifications.destroy');
});
