<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Redirige a la ruta de admin después de iniciar sesión
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // Agrupamos las rutas de admin aquí para protegerlas
    Route::prefix('dashboard')->name('admin.')->group(function () { 
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
    });
});
