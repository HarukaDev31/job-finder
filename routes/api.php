<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Importar archivos de rutas organizados por funcionalidad y roles

// Rutas públicas (sin autenticación)
require __DIR__ . '/api/public.php';

// Rutas de autenticación
require __DIR__ . '/api/auth.php';

// Rutas de usuario (postulantes)
require __DIR__ . '/api/user.php';

// Rutas de administración
require __DIR__ . '/api/admin/jobs.php';
require __DIR__ . '/api/admin/applications.php';
require __DIR__ . '/api/admin/dashboard.php';

// Ruta de prueba para verificar que la API funciona
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
