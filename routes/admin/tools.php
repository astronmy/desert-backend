<?php

use App\Http\Controllers\Admin\ToolController;
use Illuminate\Support\Facades\Route;

Route::get('herramientas', [ToolController::class, 'index'])->name('tools.index');
Route::get('herramientas/mapeo-de-listas', [ToolController::class, 'listMapping'])->name('tools.list-mapping');
Route::post('herramientas/mapeo-de-listas', [ToolController::class, 'mapList'])->name('tools.list-mapping.store');
