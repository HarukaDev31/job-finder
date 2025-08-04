<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;

// Rutas específicas para usuarios normales (postulantes)
Route::group(['middleware' => 'auth:api'], function () {
    // Trabajos disponibles para postular
    Route::get('jobs', [JobController::class, 'index']);
    Route::get('jobs/{id}', [JobController::class, 'show']);
    
    // Mis postulaciones
    Route::get('applications/my', [ApplicationController::class, 'myApplications']);
    Route::post('applications', [ApplicationController::class, 'store']);
    Route::get('applications/{id}/cv', [ApplicationController::class, 'downloadCV']);
}); 