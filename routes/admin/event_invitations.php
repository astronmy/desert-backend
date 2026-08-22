<?php

use App\Http\Controllers\Admin\EventInvitationController;
use Illuminate\Support\Facades\Route;

Route::prefix('events/{event}')->name('events.')->middleware('event.access')->group(function () {
    Route::middleware('permission:invitaciones.importar')->group(function () {
        Route::get('invitations/import', [EventInvitationController::class, 'importForm'])->name('invitations.import');
        Route::get('invitations/import/template', [EventInvitationController::class, 'importTemplate'])->name('invitations.import.template');
        Route::post('invitations/import', [EventInvitationController::class, 'import'])->name('invitations.import.store');
    });

    Route::middleware('permission:invitaciones.exportar')->group(function () {
        Route::get('invitations/export', [EventInvitationController::class, 'export'])->name('invitations.export');
    });

    Route::middleware('permission:invitaciones.moderar')->group(function () {
        Route::post('invitations/bulk', [EventInvitationController::class, 'bulk'])->name('invitations.bulk');
        Route::post('invitations/{invitation}/approve', [EventInvitationController::class, 'approve'])->name('invitations.approve');
        Route::post('invitations/{invitation}/reject', [EventInvitationController::class, 'reject'])->name('invitations.reject');
    });

    Route::middleware('permission:invitaciones.ver')->group(function () {
        Route::get('invitations', [EventInvitationController::class, 'index'])->name('invitations.index');
    });

    Route::middleware('permission:invitaciones.crear')->group(function () {
        Route::get('invitations/create', [EventInvitationController::class, 'create'])->name('invitations.create');
        Route::post('invitations', [EventInvitationController::class, 'store'])->name('invitations.store');
    });

    Route::middleware('permission:invitaciones.ver')->group(function () {
        Route::get('invitations/{invitation}', [EventInvitationController::class, 'show'])->name('invitations.show');
    });

    Route::middleware('permission:invitaciones.editar')->group(function () {
        Route::get('invitations/{invitation}/edit', [EventInvitationController::class, 'edit'])->name('invitations.edit');
        Route::put('invitations/{invitation}', [EventInvitationController::class, 'update'])->name('invitations.update');
    });

    Route::middleware('permission:invitaciones.eliminar')->group(function () {
        Route::delete('invitations/{invitation}', [EventInvitationController::class, 'destroy'])->name('invitations.destroy');
    });
});
