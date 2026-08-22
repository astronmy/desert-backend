<?php

use App\Http\Controllers\Admin\EventInvitationController;
use Illuminate\Support\Facades\Route;

Route::prefix('events/{event}')->name('events.')->group(function () {
    Route::get('invitations/import', [EventInvitationController::class, 'importForm'])->name('invitations.import');
    Route::get('invitations/import/template', [EventInvitationController::class, 'importTemplate'])->name('invitations.import.template');
    Route::post('invitations/import', [EventInvitationController::class, 'import'])->name('invitations.import.store');

    Route::post('invitations/{invitation}/deeplink', [EventInvitationController::class, 'generateDeeplink'])
        ->name('invitations.deeplink');

    Route::resource('invitations', EventInvitationController::class);
});
