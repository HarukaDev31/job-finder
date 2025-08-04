<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Crear 10 administradores
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        // Crear un admin específico para testing
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@jobfinder.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
