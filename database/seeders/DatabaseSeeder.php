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
            BloodTypeSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            
        ]);
    }
}