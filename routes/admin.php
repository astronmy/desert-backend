<?php

use App\Http\Controllers\Admin\AccessControlLogController;
use App\Http\Controllers\Admin\ClientEventController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MicroserviceLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.ver')
        ->name('dashboard');

    Route::post('dashboard/registration-link', [DashboardController::class, 'generateLink'])
        ->middleware('permission:deeplink.generar')
        ->name('dashboard.registration-link');

    Route::post('dashboard/invitations/bulk-approve', [DashboardController::class, 'bulkApprove'])
        ->middleware('permission:invitaciones.moderar')
        ->name('dashboard.invitations.bulk-approve');

    Route::post('dashboard/invitations/{invitation}/approve', [DashboardController::class, 'approve'])
        ->middleware('permission:invitaciones.moderar')
        ->name('dashboard.invitations.approve');

    Route::get('mi-evento', [ClientEventController::class, 'edit'])
        ->middleware('permission:eventos.contenido')
        ->name('client-event.edit');

    Route::put('mi-evento', [ClientEventController::class, 'update'])
        ->middleware('permission:eventos.contenido')
        ->name('client-event.update');

    Route::get('access-control-logs', [AccessControlLogController::class, 'index'])
        ->middleware('permission:logs.ver')
        ->name('access-control-logs.index');

    Route::get('microservice-logs', [MicroserviceLogController::class, 'index'])
        ->middleware('permission:logs.ver')
        ->name('microservice-logs.index');

    require __DIR__.'/admin/event_accesses.php';
    require __DIR__.'/admin/event_invitations.php';
    require __DIR__.'/admin/events.php';
    require __DIR__.'/admin/manual.php';
    require __DIR__.'/admin/notifications.php';
    require __DIR__.'/admin/roles.php';
    require __DIR__.'/admin/users.php';
});
