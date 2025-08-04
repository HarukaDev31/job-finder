<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;

// Rutas de administración de aplicaciones
Route::group(['prefix' => 'admin', 'middleware' => ['auth:api', 'admin']], function () {
    Route::get('applications', [ApplicationController::class, 'index']);
    Route::put('applications/{id}/status', [ApplicationController::class, 'updateStatus']);
    Route::get('jobs/{jobId}/applications', [ApplicationController::class, 'jobApplications']);
    Route::get('applications/stats', [ApplicationController::class, 'stats']);
}); 