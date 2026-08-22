<?php

use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:roles.ver')->group(function () {
    Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
});

Route::middleware('permission:roles.crear')->group(function () {
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
});

Route::middleware('permission:roles.editar')->group(function () {
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update');
});

Route::middleware('permission:roles.eliminar')->group(function () {
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});
