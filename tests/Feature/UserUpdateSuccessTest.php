<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('un usuario puede actualizar su nombre correctamente', function () {
    // 1. Crear usuario
    $user = User::factory()->create(['name' => 'Nombre Viejo']);

    // 2. Crear un rol manualmente para tener un ID válido
    $roleId = DB::table('roles')->insertGetId([
        'name' => 'Test Role ' . rand(100,999),
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. Generar datos falsos con el Factory
    $newData = User::factory()->make()->toArray();
    
    // 4. Preparar datos para el UPDATE
    $newData['name'] = 'Nombre Nuevo';
    $newData['email'] = $user->email;
    $newData['role_id'] = $roleId;
    $newData['id_number'] = '12345678'; 
    // -----------------------

    // Limpieza de datos
    unset($newData['password']);
    unset($newData['email_verified_at']);
    unset($newData['two_factor_confirmed_at']);
    unset($newData['remember_token']);
    unset($newData['profile_photo_path']);

    // 5. Enviar petición
    $response = $this->actingAs($user)
        ->put(route('admin.users.update', $user), $newData);

    // 6. Validaciones
    $response->assertSessionHasNoErrors();
    $response->assertStatus(302);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nombre Nuevo',
    ]);
});