<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // 2. Crea tu usuario de prueba
        User::factory()->create([
            'name' => 'Fernely Flores',
            'email' => 'admin@admin.admin',
            'password'=> bcrypt('password'),
            'id_number'=>'1234567890',
            'phone'=>'0987654321',
            'address'=>'Calle 123, colonia centro',
        ])->assignRole('Doctor');
    }
}
