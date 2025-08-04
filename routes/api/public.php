<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;

// Rutas públicas (sin autenticación)
Route::get('stats', [DashboardController::class, 'stats']);
Route::get('jobs/recent', [JobController::class, 'recent']); 