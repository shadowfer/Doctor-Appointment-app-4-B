<?php
use App\Http\Controllers\Admin\DoctorController;
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
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\DoctorScheduleController;

//Gestion de Doctores
Route::resource('doctors', DoctorController::class);

// Gestión de Citas Médicas
Route::resource('appointments', AppointmentController::class);
Route::get('appointments/{appointment}/consultation', App\Livewire\Admin\Appointments\ManageConsultation::class)->name('appointments.consultation');

// Gestión de Horarios de Doctores
Route::get('doctors/{doctor}/schedules', [DoctorController::class, 'schedules'])->name('doctors.schedules');
Route::put('doctors/{doctor}/schedules', [DoctorController::class, 'updateSchedules'])->name('doctors.schedules.update');