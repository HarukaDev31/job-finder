<?php

namespace Database\Seeders;

use App\Models\Postulante;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class PostulanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Crear 1000 usuarios postulantes
        for ($i = 0; $i < 100; $i++) {
            $nombres = $faker->firstName();
            $apellidos = $faker->lastName() . ' ' . $faker->lastName();
            $email = $faker->unique()->safeEmail();
            $numeroDocumento = $faker->unique()->numerify('########');
            $tipoDocumento = $faker->randomElement(['CC', 'CE', 'TI', 'PP']);
            $fechaNacimiento = $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d');

            $user = User::create([
                'name' => $nombres . ' ' . $apellidos,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'postulante',
            ]);

            Postulante::create([
                'user_id' => $user->id,
                'numero_documento' => $numeroDocumento,
                'tipo_documento' => $tipoDocumento,
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'fecha_nacimiento' => $fechaNacimiento,
            ]);
        }

        // Crear algunos postulantes específicos para testing
        $postulantes = [
            [
                'nombres' => 'Juan Carlos',
                'apellidos' => 'García López',
                'email' => 'juan.garcia@email.com',
                'numero_documento' => '12345678',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => '1990-05-15',
            ],
            [
                'nombres' => 'María Fernanda',
                'apellidos' => 'Rodríguez Silva',
                'email' => 'maria.rodriguez@email.com',
                'numero_documento' => '87654321',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => '1988-12-03',
            ],
            [
                'nombres' => 'Carlos Andrés',
                'apellidos' => 'Martínez Pérez',
                'email' => 'carlos.martinez@email.com',
                'numero_documento' => '11223344',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => '1992-08-22',
            ],
        ];

        foreach ($postulantes as $postulanteData) {
            $user = User::create([
                'name' => $postulanteData['nombres'] . ' ' . $postulanteData['apellidos'],
                'email' => $postulanteData['email'],
                'password' => Hash::make('password'),
                'role' => 'postulante',
            ]);

            Postulante::create([
                'user_id' => $user->id,
                'numero_documento' => $postulanteData['numero_documento'],
                'tipo_documento' => $postulanteData['tipo_documento'],
                'nombres' => $postulanteData['nombres'],
                'apellidos' => $postulanteData['apellidos'],
                'fecha_nacimiento' => $postulanteData['fecha_nacimiento'],
            ]);
        }
    }
}
