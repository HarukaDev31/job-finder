<?php

namespace Database\Seeders;

use App\Models\Postulacion;
use App\Models\Postulante;
use App\Models\Trabajo;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PostulacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Obtener todos los postulantes y trabajos
        $postulantes = Postulante::with('user')->get();
        $trabajos = Trabajo::where('activo', true)->get();

        if ($postulantes->isEmpty() || $trabajos->isEmpty()) {
            $this->command->warn('No hay postulantes o trabajos disponibles para crear postulaciones.');
            return;
        }

        // Crear postulaciones aleatorias
        // Cada postulante tendrá entre 1 y 5 postulaciones
        foreach ($postulantes as $postulante) {
            $numPostulaciones = $faker->numberBetween(1, 5);
            $trabajosAleatorios = $trabajos->random($numPostulaciones);

            foreach ($trabajosAleatorios as $trabajo) {
                // Verificar que no exista ya una postulación para este postulante y trabajo
                $existePostulacion = Postulacion::where('postulante_id', $postulante->id)
                    ->where('trabajo_id', $trabajo->id)
                    ->exists();

                if (!$existePostulacion) {
                    $estados = ['pendiente', 'revisado', 'aceptado', 'rechazado'];
                    $estado = $faker->randomElement($estados);
                    
                    // Los estados 'aceptada' y 'rechazada' son menos probables
                    if ($estado === 'aceptada' || $estado === 'rechazada') {
                        $estado = $faker->randomElement(['pendiente', 'revisando', 'pendiente', 'revisando', 'pendiente']);
                    }

                    $mensajes = [
                        'Me interesa mucho esta oportunidad y creo que puedo aportar valor a su empresa.',
                        'Tengo experiencia en el área y me gustaría formar parte de su equipo.',
                        'Soy una persona responsable y comprometida con el trabajo.',
                        'Busco una oportunidad para crecer profesionalmente en su empresa.',
                        'Tengo las habilidades necesarias para este puesto y estoy dispuesto a aprender.',
                        'Me apasiona este tipo de trabajo y tengo experiencia relacionada.',
                        'Soy una persona proactiva y trabajo bien en equipo.',
                        'Tengo los conocimientos técnicos requeridos y me gusta enfrentar nuevos desafíos.',
                        'Busco una empresa donde pueda desarrollar mi potencial.',
                        'Tengo experiencia previa en roles similares y me adapto rápidamente.'
                    ];

                    Postulacion::create([
                        'postulante_id' => $postulante->id,
                        'trabajo_id' => $trabajo->id,
                        'mensaje' => $faker->randomElement($mensajes) . ' ' . $faker->paragraph(1),
                        'estado' => $estado,
                        'cv_path' => 'cvs/sample_cv.pdf', // CV de ejemplo
                    ]);
                }
            }
        }

        $this->command->info('Postulaciones creadas exitosamente.');
    }
} 