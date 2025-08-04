<?php

use Illuminate\Support\Facades\Route;

// Ruta principal que sirve la aplicación Vue
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api).*');
