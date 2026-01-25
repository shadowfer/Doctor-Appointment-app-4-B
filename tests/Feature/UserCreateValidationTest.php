<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('no se puede crear un usuario con un email duplicado', function () {
    // 1. Crear un usuario existente en la BD
    $existingUser = User::factory()->create(['email' => 'duplicado@test.com']);
    
    // 2. Crear un usuario admin que intentará crear el nuevo registro
    $admin = User::factory()->create();

    // 3. Intentar crear otro usuario con el mismo email
    $response = $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'duplicado@test.com', // El mismo email que ya existe
            'password' => 'password123',
            'password_confirmation' => 'password123', // Por si tu controlador pide confirmación
        ]);

    // 4. Esperar error de validación en el campo email
    $response->assertSessionHasErrors(['email']);
    
    // 5. Verificar que solo existen los 2 usuarios originales (el existente + el admin)
    $this->assertDatabaseCount('users', 2);
});