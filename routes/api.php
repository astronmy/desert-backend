<?php

use App\Http\Controllers\Api\AccessController;
use App\Http\Controllers\Api\DeeplinkController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\EventNotificationController;
use App\Http\Controllers\Api\EventRegistrationController;
use App\Http\Controllers\Api\InvitationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->group(function () {
    Route::get('events', [EventController::class, 'index']);
    Route::get('invitations/{code}', [InvitationController::class, 'show']);
    Route::post('invitations/{code}/confirm', [InvitationController::class, 'confirm']);
    Route::post('events/{event}/register', [EventRegistrationController::class, 'store']);
});

Route::middleware('throttle:60,1')->group(function () {
    Route::get('invitations/{code}/entry', [InvitationController::class, 'entry']);
    Route::post('accesses', [AccessController::class, 'store']);
    Route::post('deeplink/redeem', [DeeplinkController::class, 'redeem']);
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('notifications', [EventNotificationController::class, 'index'])
        ->middleware('permission:notificaciones.ver');
    Route::post('notifications', [EventNotificationController::class, 'store'])
        ->middleware('permission:notificaciones.crear');
    Route::get('notifications/{event_notification}', [EventNotificationController::class, 'show'])
        ->middleware('permission:notificaciones.ver');
    Route::put('notifications/{event_notification}', [EventNotificationController::class, 'update'])
        ->middleware('permission:notificaciones.editar');
    Route::delete('notifications/{event_notification}', [EventNotificationController::class, 'destroy'])
        ->middleware('permission:notificaciones.eliminar');
    Route::get('events/{event}/notifiable-invitations', [EventNotificationController::class, 'notifiableInvitations'])
        ->middleware(['permission:notificaciones.ver', 'event.access']);
});
