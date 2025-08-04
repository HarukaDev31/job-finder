<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;

// Rutas de administración de trabajos
Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'admin']], function () {
    Route::get('jobs', [JobController::class, 'index']);
    Route::post('jobs', [JobController::class, 'store']);
    Route::put('jobs/{id}', [JobController::class, 'update']);
    Route::delete('jobs/{id}', [JobController::class, 'destroy']);
}); 