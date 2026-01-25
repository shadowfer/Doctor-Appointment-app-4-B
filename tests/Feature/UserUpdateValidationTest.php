<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('la actualizacion falla si el nombre esta vacio', function () {
    // 1. Crear un usuario
    $user = User::factory()->create(['name' => 'Nombre Original']);

    // 2. Intentar actualizar con nombre vacío
    $response = $this->actingAs($user)
        ->put(route('admin.users.update', $user), [
            'name' => '', // Inválido
            'email' => $user->email,
        ]);

    // 3. Verificar que hay error de validación en 'name'
    $response->assertSessionHasErrors(['name']);
    
    // 4. Verificar en BD que el nombre NO cambió
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Nombre Original',
    ]);
});