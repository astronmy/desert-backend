<?php

use App\Http\Controllers\Admin\ManualController;
use Illuminate\Support\Facades\Route;

// No extra permission gate: every authenticated user (any role) can read the
// manual. Protection against outside access comes from the 'auth' middleware
// applied to the whole admin group in routes/admin.php — there is no public,
// unauthenticated URL for these files (they live in resources/, not public/).
Route::prefix('manual')->name('manual.')->group(function () {
    Route::get('/', [ManualController::class, 'show'])->name('show');
    Route::get('manual-desert.pdf', [ManualController::class, 'pdf'])->name('pdf');
    Route::get('screenshots/{file}', [ManualController::class, 'screenshot'])->name('screenshot');
});
