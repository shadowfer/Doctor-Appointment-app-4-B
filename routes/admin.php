<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view(('admin.dashboard'));
})->name('dashboard');

//Gestio de roles
Route::resource('roles', App\Http\Controllers\Admin\RoleController::class)->names('admin.roles');   