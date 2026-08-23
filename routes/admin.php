<?php

use App\Http\Controllers\Admin\DashboardController;
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

    require __DIR__.'/admin/event_accesses.php';
    require __DIR__.'/admin/event_invitations.php';
    require __DIR__.'/admin/events.php';
    require __DIR__.'/admin/notifications.php';
    require __DIR__.'/admin/roles.php';
    require __DIR__.'/admin/users.php';
});
