<?php

namespace App\Services;

use App\Models\Trabajo;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class JobService
{
    /**
     * Obtener trabajos con filtros y paginación
     */
    public function getJobs(array $filters = []): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;
        $search = $filters['search'] ?? '';
        $active = $filters['active'] ?? null;
        $minSalary = $filters['min_salary'] ?? null;
        $maxSalary = $filters['max_salary'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'created_at';

        $query = Trabajo::withCount('postulaciones');

        // Filtro de búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('titulo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro de estado activo
        if ($active !== null) {
            $query->where('activo', $active);
        }

        // Filtro de sueldo mínimo
        if ($minSalary !== null && $minSalary !== '') {
            $query->where('sueldo', '>=', $minSalary);
        }

        // Filtro de sueldo máximo
        if ($maxSalary !== null && $maxSalary !== '') {
            $query->where('sueldo', '<=', $maxSalary);
        }

        // Ordenamiento
        $sortDirection = $this->getSortDirection($sortBy);

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    /**
     * Obtener un trabajo específico
     */
    public function getJob(int $id): Trabajo
    {
        return Trabajo::withCount('postulaciones')
            ->with('postulaciones.postulante.user')
            ->findOrFail($id);
    }

    /**
     * Crear un nuevo trabajo
     */
    public function createJob(array $data): Trabajo
    {
        return DB::transaction(function () use ($data) {
            return Trabajo::create([
                'titulo' => $data['titulo'],
                'descripcion' => $data['descripcion'],
                'sueldo' => $data['sueldo']
            ]);
        });
    }

    /**
     * Obtener trabajos recientes
     */
    public function getRecentJobs(int $limit = 6): \Illuminate\Database\Eloquent\Collection
    {
        return Trabajo::withCount('postulaciones')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Obtener estadísticas de trabajos
     */
    public function getJobStats(): array
    {
        return [
            'total' => Trabajo::count(),
            'active' => Trabajo::where('activo', true)->count(),
            'recent' => Trabajo::where('created_at', '>=', now()->subDays(30))->count()
        ];
    }

    /**
     * Determinar la dirección de ordenamiento
     */
    private function getSortDirection(string $sortBy): string
    {
        return match ($sortBy) {
            'sueldo' => 'desc',
            'titulo' => 'asc',
            default => 'desc'
        };
    }
} 