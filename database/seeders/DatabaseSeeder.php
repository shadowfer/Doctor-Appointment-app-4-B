<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Llama a tu RoleSeeder (el que acabas de mostrar)
        $this->call([
            RoleSeeder::class
        ]);

        // 2. Crea tu usuario de prueba
        User::factory()->create([
            'name' => 'Fernely Flores',
            'email' => 'admin@admin.admin',
            'password'=> bcrypt('password'),
        ]);
    }
}