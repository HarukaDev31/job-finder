<?php

namespace Database\Factories;

use App\Models\Postulante;
use App\Models\Trabajo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Postulacion>
 */
class PostulacionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'postulante_id' => Postulante::factory(),
            'trabajo_id' => Trabajo::factory(),
            'mensaje' => fake()->paragraphs(2, true),
            'cv_path' => 'cvs/' . fake()->uuid() . '.pdf',
            'estado' => fake()->randomElement(['pendiente', 'revisado', 'aceptado', 'rechazado']),
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
} 