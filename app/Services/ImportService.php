<?php

namespace App\Services;

use App\Models\Trabajo;
use App\Models\Postulante;
use App\Models\Postulacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ImportService
{
    private $stats = [
        'trabajos' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
        'postulantes' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
        'postulaciones' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0],
        'usuarios' => ['creados' => 0, 'actualizados' => 0, 'errores' => 0]
    ];

    public function importFromExcel($file, $userId = null)
    {
        try {
            DB::beginTransaction();

            $data = Excel::load($file)->get();
            $output = "=== RESULTADOS DE LA IMPORTACIÓN ===\n";

            foreach ($data as $row) {
                $this->processRow($row, $userId);
            }

            $output .= $this->generateReport();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Importación completada exitosamente',
                'output' => $output
            ];

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error en importación Excel: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Error durante la importación: ' . $e->getMessage()
            ];
        }
    }

    private function processRow($row, $userId)
    {
        try {
            $tipo = strtoupper(trim($row->tipo ?? ''));
            
            if ($tipo === 'TRABAJO') {
                $this->processTrabajo($row, $userId);
            } elseif ($tipo === 'POSTULANTE') {
                $this->processPostulante($row, $userId);
            } elseif ($tipo === 'POSTULACION') {
                $this->processPostulacion($row, $userId);
            } else {
                $this->stats['trabajos']['errores']++;
                Log::warning('Tipo no reconocido: ' . $tipo);
            }

        } catch (\Exception $e) {
            $this->stats['trabajos']['errores']++;
            Log::error('Error procesando fila: ' . $e->getMessage());
        }
    }

    private function processTrabajo($row, $userId)
    {
        $titulo = trim($row->titulo ?? '');
        $descripcion = trim($row->descripcion ?? '');
        $sueldo = floatval($row->sueldo ?? 0);

        if (empty($titulo) || empty($descripcion) || $sueldo <= 0) {
            $this->stats['trabajos']['errores']++;
            return;
        }

        $trabajo = Trabajo::where('titulo', $titulo)->first();

        if ($trabajo) {
            $trabajo->update([
                'descripcion' => $descripcion,
                'sueldo' => $sueldo,
                'activo' => true
            ]);
            $this->stats['trabajos']['actualizados']++;
        } else {
            Trabajo::create([
                'titulo' => $titulo,
                'descripcion' => $descripcion,
                'sueldo' => $sueldo,
                'activo' => true
            ]);
            $this->stats['trabajos']['creados']++;
        }
    }

    private function processPostulante($row, $userId)
    {
        $email = trim($row->email ?? '');
        $nombre = trim($row->nombre ?? '');
        $documento = trim($row->documento ?? '');

        if (empty($email) || empty($nombre) || empty($documento)) {
            $this->stats['postulantes']['errores']++;
            return;
        }

        // Crear o actualizar usuario
        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => bcrypt('password123'), // Contraseña temporal
                'role' => 'postulante'
            ]);
            $this->stats['usuarios']['creados']++;
        } else {
            $user->update(['name' => $nombre]);
            $this->stats['usuarios']['actualizados']++;
        }

        // Crear o actualizar postulante
        $postulante = Postulante::where('user_id', $user->id)->first();

        if ($postulante) {
            $postulante->update([
                'numero_documento' => $documento,
                'nombres' => $nombre,
                'apellidos' => '',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => Carbon::now()->subYears(25)
            ]);
            $this->stats['postulantes']['actualizados']++;
        } else {
            Postulante::create([
                'user_id' => $user->id,
                'numero_documento' => $documento,
                'nombres' => $nombre,
                'apellidos' => '',
                'tipo_documento' => 'CC',
                'fecha_nacimiento' => Carbon::now()->subYears(25)
            ]);
            $this->stats['postulantes']['creados']++;
        }
    }

    private function processPostulacion($row, $userId)
    {
        $trabajoId = intval($row->trabajo_id ?? 0);
        $postulanteId = intval($row->postulante_id ?? 0);
        $mensaje = trim($row->mensaje ?? '');
        $estado = trim($row->estado ?? 'pendiente');

        if ($trabajoId <= 0 || $postulanteId <= 0 || empty($mensaje)) {
            $this->stats['postulaciones']['errores']++;
            return;
        }

        $postulacion = Postulacion::where('trabajo_id', $trabajoId)
            ->where('postulante_id', $postulanteId)
            ->first();

        if ($postulacion) {
            $postulacion->update([
                'mensaje' => $mensaje,
                'estado' => $estado
            ]);
            $this->stats['postulaciones']['actualizados']++;
        } else {
            Postulacion::create([
                'trabajo_id' => $trabajoId,
                'postulante_id' => $postulanteId,
                'mensaje' => $mensaje,
                'estado' => $estado
            ]);
            $this->stats['postulaciones']['creados']++;
        }
    }

    private function generateReport()
    {
        $report = "\nTrabajos:\n";
        $report .= "  - Creados: " . $this->stats['trabajos']['creados'] . "\n";
        $report .= "  - Actualizados: " . $this->stats['trabajos']['actualizados'] . "\n";
        $report .= "  - Errores: " . $this->stats['trabajos']['errores'] . "\n";

        $report .= "\nPostulantes:\n";
        $report .= "  - Creados: " . $this->stats['postulantes']['creados'] . "\n";
        $report .= "  - Actualizados: " . $this->stats['postulantes']['actualizados'] . "\n";
        $report .= "  - Errores: " . $this->stats['postulantes']['errores'] . "\n";

        $report .= "\nPostulaciones:\n";
        $report .= "  - Creados: " . $this->stats['postulaciones']['creados'] . "\n";
        $report .= "  - Actualizados: " . $this->stats['postulaciones']['actualizados'] . "\n";
        $report .= "  - Errores: " . $this->stats['postulaciones']['errores'] . "\n";

        $report .= "\nUsuarios:\n";
        $report .= "  - Creados: " . $this->stats['usuarios']['creados'] . "\n";
        $report .= "  - Actualizados: " . $this->stats['usuarios']['actualizados'] . "\n";
        $report .= "  - Errores: " . $this->stats['usuarios']['errores'] . "\n";

        $total = array_sum(array_column($this->stats['trabajos'], 'creados')) +
                array_sum(array_column($this->stats['postulantes'], 'creados')) +
                array_sum(array_column($this->stats['postulaciones'], 'creados'));

        $report .= "\nTotal de registros procesados: " . $total . "\n";

        return $report;
    }

    public function getStats()
    {
        return [
            'trabajos' => Trabajo::count(),
            'postulantes' => Postulante::count(),
            'postulaciones' => Postulacion::count(),
            'usuarios' => User::where('role', 'postulante')->count()
        ];
    }
} 