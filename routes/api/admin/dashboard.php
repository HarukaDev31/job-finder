<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Rutas de dashboard administrativo
Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'admin']], function () {
    Route::get('metrics', [DashboardController::class, 'metrics']);
}); 