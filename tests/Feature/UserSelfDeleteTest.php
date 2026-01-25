<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos la cualidad para refrescar la base de datos en cada prueba
uses(RefreshDatabase::class);

test('Un usuario no puede eliminarse a sí mismo', function () {


    // Crear un usuario de prueba
    $user = User::factory()->create();

    // Autenticar al usuario
    $this->actingAs($user, 'web');

    // Intentar eliminarse a sí mismo
    $response = $this->delete(route('admin.users.destroy', $user));

    // Verificar que la respuesta sea un error 403 (Prohibido)
    $response->assertStatus(403);

    // Verificar que el usuario aún exista en la base de datos
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});
