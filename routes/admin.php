<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    require __DIR__.'/admin/event_invitations.php';
    require __DIR__.'/admin/events.php';
    require __DIR__.'/admin/users.php';
});
