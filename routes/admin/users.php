<?php

use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('permission:usuarios.ver')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
});

Route::middleware('permission:usuarios.crear')->group(function () {
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
});

Route::middleware('permission:usuarios.editar')->group(function () {
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
});

Route::middleware('permission:usuarios.eliminar')->group(function () {
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
