<?php

use App\Http\Controllers\Api\AccessController;
use App\Http\Controllers\Api\InvitationController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->group(function () {
    Route::get('invitations/{code}', [InvitationController::class, 'show']);
    Route::post('invitations/{code}/confirm', [InvitationController::class, 'confirm']);
});

Route::middleware('throttle:60,1')->group(function () {
    Route::get('invitations/{code}/entry', [InvitationController::class, 'entry']);
    Route::post('accesses', [AccessController::class, 'store']);
});
