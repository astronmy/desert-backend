<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.ver')
        ->name('dashboard');

    require __DIR__.'/admin/event_accesses.php';
    require __DIR__.'/admin/event_invitations.php';
    require __DIR__.'/admin/events.php';
    require __DIR__.'/admin/roles.php';
    require __DIR__.'/admin/users.php';
});
