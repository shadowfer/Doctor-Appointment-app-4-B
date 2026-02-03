<?php
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){
    return view ('admin.dashboard');
})->name('dashboard');

//Gestion de roles
Route::resource('roles', RoleController::class);
//Gestion de Usuarios
Route::resource('users', UserController::class);
//Gestion de Pacientes
Route::resource('patients', PatientController::class);