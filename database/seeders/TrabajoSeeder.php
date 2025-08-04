<?php

namespace Database\Seeders;

use App\Models\Trabajo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class TrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        // Crear 10000 trabajos
        for ($i = 0; $i < 10000; $i++) {
            $titulos = [
                'Desarrollador Full Stack', 'Desarrollador Frontend', 'Desarrollador Backend',
                'Diseñador UX/UI', 'Diseñador Gráfico', 'Diseñador Web',
                'Analista de Datos', 'Científico de Datos', 'Analista de Negocios',
                'DevOps Engineer', 'Ingeniero de Sistemas', 'Arquitecto de Software',
                'Project Manager', 'Scrum Master', 'Product Owner',
                'QA Engineer', 'Tester Manual', 'Tester Automatizado',
                'Marketing Digital', 'SEO Specialist', 'Content Manager',
                'Ventas', 'Customer Success', 'Soporte Técnico',
                'Recursos Humanos', 'Contador', 'Abogado',
                'Médico', 'Enfermero', 'Psicólogo',
                'Profesor', 'Traductor', 'Periodista',
                'Chef', 'Bartender', 'Mesero',
                'Conductor', 'Mecánico', 'Electricista',
                'Arquitecto', 'Ingeniero Civil', 'Diseñador de Interiores'
            ];

            $descripciones = [
                'Buscamos un profesional con experiencia en desarrollo web y tecnologías modernas.',
                'Necesitamos alguien creativo y con habilidades técnicas sólidas.',
                'Buscamos un analista con experiencia en datos y herramientas de análisis.',
                'Necesitamos un ingeniero con experiencia en infraestructura y automatización.',
                'Buscamos un líder de proyecto con experiencia en metodologías ágiles.',
                'Necesitamos un profesional con experiencia en testing y calidad de software.',
                'Buscamos un especialista en marketing digital y estrategias online.',
                'Necesitamos un profesional de ventas con experiencia en el sector.',
                'Buscamos un profesional de recursos humanos con experiencia en reclutamiento.',
                'Necesitamos un profesional de la salud con experiencia en atención al paciente.',
                'Buscamos un educador con experiencia en enseñanza y metodologías pedagógicas.',
                'Necesitamos un profesional de la comunicación con experiencia en medios.',
                'Buscamos un profesional de la gastronomía con experiencia en cocina.',
                'Necesitamos un profesional del transporte con experiencia en conducción.',
                'Buscamos un técnico con experiencia en mantenimiento y reparación.',
                'Necesitamos un profesional de la construcción con experiencia en proyectos.',
                'Buscamos un profesional del diseño con experiencia en proyectos arquitectónicos.'
            ];

            $titulo = $faker->randomElement($titulos);
            $descripcion = $faker->randomElement($descripciones) . ' ' . $faker->paragraph(2);
            $sueldo = $faker->numberBetween(1000000, 8000000);
            $activo = $faker->boolean(80); // 80% de probabilidad de estar activo

            Trabajo::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'sueldo' => $sueldo,
                'activo' => $activo,
            ]);
        }

        // Crear algunos trabajos específicos para testing
        $trabajos = [
            [
                'titulo' => 'Desarrollador Full Stack',
                'descripcion' => 'Buscamos un desarrollador full stack con experiencia en Laravel, Vue.js y MySQL. Responsabilidades incluyen desarrollo de aplicaciones web, mantenimiento de código existente y colaboración con el equipo de desarrollo.',
                'sueldo' => 3500000,
                'activo' => true,
            ],
            [
                'titulo' => 'Diseñador UX/UI',
                'descripcion' => 'Necesitamos un diseñador UX/UI creativo y experimentado. Debe tener habilidades en Figma, Adobe Creative Suite y experiencia en diseño de interfaces de usuario.',
                'sueldo' => 2800000,
                'activo' => true,
            ],
            [
                'titulo' => 'Analista de Datos',
                'descripcion' => 'Buscamos un analista de datos con experiencia en Python, SQL y herramientas de visualización. Responsabilidades incluyen análisis de datos, creación de reportes y presentaciones.',
                'sueldo' => 3200000,
                'activo' => true,
            ],
            [
                'titulo' => 'DevOps Engineer',
                'descripcion' => 'Necesitamos un ingeniero DevOps con experiencia en AWS, Docker, Kubernetes y CI/CD. Responsabilidades incluyen gestión de infraestructura y automatización de procesos.',
                'sueldo' => 4000000,
                'activo' => true,
            ],
            [
                'titulo' => 'Project Manager',
                'descripcion' => 'Buscamos un project manager con experiencia en metodologías ágiles. Responsabilidades incluyen gestión de proyectos, coordinación de equipos y seguimiento de entregables.',
                'sueldo' => 3800000,
                'activo' => true,
            ],
        ];

        foreach ($trabajos as $trabajo) {
            Trabajo::create($trabajo);
        }
    }
}
