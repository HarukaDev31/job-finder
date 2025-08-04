<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

require __DIR__ . '/api/auth.php';
require __DIR__ . '/api/public.php';
require __DIR__ . '/api/user.php';
require __DIR__ . '/api/admin/applications.php';
require __DIR__ . '/api/admin/dashboard.php';
require __DIR__ . '/api/admin/jobs.php';
