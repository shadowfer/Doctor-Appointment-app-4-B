<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController; 

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Redirección de la raíz / a /admin
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('dashboard');

    // --- GRUPO DE RUTAS DE ADMINISTRADOR ---
    // Aplica el prefijo de URL "/admin" y el prefijo de nombre "admin."
    Route::prefix('admin')->name('admin.')->group(function () {

        // 1. Dashboard (URL: /admin, Nombre: admin.dashboard)
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // 2. Roles (URL: /admin/roles, Nombres: admin.roles.index, admin.roles.create, etc.)
        // Esto define todas las rutas de recursos que necesitas para la ADA 1.
        Route::resource('roles', RoleController::class); 

    });
});
// ... (El resto de las rutas de autenticación de Jetstream/Breeze)
