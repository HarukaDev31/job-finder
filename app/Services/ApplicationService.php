<?php

namespace App\Services;

use App\Models\Postulacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ApplicationService
{
    /**
     * Obtener aplicaciones del usuario con filtros y paginación
     */
    public function getUserApplications(User $user, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $status = $filters['status'] ?? '';

        $query = $user->postulante->postulaciones()->with('trabajo');

        // Filtro por estado
        if ($status) {
            $query->where('estado', $status);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Obtener todas las aplicaciones para admin con filtros y paginación
     */
    public function getAllApplications(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $status = $filters['status'] ?? '';
        $search = $filters['search'] ?? '';

        $query = Postulacion::with(['trabajo', 'postulante.user']);

        // Filtro por estado
        if ($status) {
            $query->where('estado', $status);
        }

        // Filtro de búsqueda
        if ($search) {
            $query->whereHas('postulante.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('trabajo', function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Obtener aplicaciones de un trabajo específico
     */
    public function getJobApplications(int $jobId, array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $status = $filters['status'] ?? '';

        $query = Postulacion::with(['postulante.user', 'trabajo'])
            ->where('trabajo_id', $jobId);

        // Filtrar por estado si se proporciona
        if ($status) {
            $query->where('estado', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Crear una nueva aplicación
     */
    public function createApplication(User $user, array $data): Postulacion
    {
        return DB::transaction(function () use ($user, $data) {
            // Verificar que no se haya postulado antes
            $existingApplication = Postulacion::where('postulante_id', $user->postulante->id)
                ->where('trabajo_id', $data['trabajo_id'])
                ->first();

            if ($existingApplication) {
                throw new \Exception('Ya te has postulado a este trabajo');
            }

            // Guardar el archivo CV
            $cvPath = $data['cv']->store('cvs', 'public');

            // Crear la aplicación
            return Postulacion::create([
                'postulante_id' => $user->postulante->id,
                'trabajo_id' => $data['trabajo_id'],
                'mensaje' => $data['mensaje'],
                'cv_path' => $cvPath,
                'estado' => 'pendiente'
            ]);
        });
    }

    /**
     * Actualizar el estado de una aplicación
     */
    public function updateApplicationStatus(int $applicationId, string $status): Postulacion
    {
        return DB::transaction(function () use ($applicationId, $status) {
            $application = Postulacion::findOrFail($applicationId);
            $application->update(['estado' => $status]);
            
            return $application->load('trabajo', 'postulante.user');
        });
    }

    /**
     * Descargar CV de una aplicación
     */
    public function downloadCV(int $applicationId, User $user): array
    {
        $application = Postulacion::with(['postulante.user', 'trabajo'])
            ->findOrFail($applicationId);

        // Verificar permisos
        $isAdmin = $user->role === 'admin';
        
        if (!$isAdmin && $application->postulante->user_id !== $user->id) {
            throw new \Exception('No tienes permisos para descargar este CV');
        }

        // Verificar que el CV existe
        if (!$application->cv_path || !file_exists(storage_path('app/public/' . $application->cv_path))) {
            throw new \Exception('El archivo CV no existe');
        }

        $filePath = storage_path('app/public/' . $application->cv_path);
        $fileName = basename($application->cv_path);
        
        // Generar nombre de archivo descriptivo
        $applicantName = $application->postulante->user->name;
        $jobTitle = $application->trabajo->titulo;
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $downloadName = "CV_{$applicantName}_{$jobTitle}.{$extension}";
        
        // Limpiar el nombre del archivo para que sea válido
        $downloadName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $downloadName);

        return [
            'file_path' => $filePath,
            'download_name' => $downloadName
        ];
    }

    /**
     * Obtener estadísticas de aplicaciones
     */
    public function getApplicationStats(): array
    {
        return [
            'total' => Postulacion::count(),
            'pending' => Postulacion::where('estado', 'pendiente')->count(),
            'reviewed' => Postulacion::where('estado', 'revisado')->count(),
            'accepted' => Postulacion::where('estado', 'aceptado')->count(),
            'rejected' => Postulacion::where('estado', 'rechazado')->count()
        ];
    }
} 