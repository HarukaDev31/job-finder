<?php

namespace App\Services;

use App\Models\Trabajo;
use App\Models\Postulacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Obtener estadísticas generales del portal
     */
    public function getStats(): array
    {
        return [
            'jobs' => Trabajo::count(),
            'users' => User::count(),
            'applications' => Postulacion::count(),
            'companies' => 1 // Por ahora solo una empresa
        ];
    }

    /**
     * Obtener métricas del dashboard para administrador
     */
    public function getMetrics(): array
    {
        // Métricas generales
        $totalJobs = Trabajo::count();
        $totalApplications = Postulacion::count();
        $totalUsers = User::count();
        $pendingApplications = Postulacion::where('estado', 'pendiente')->count();
        
        // Métricas adicionales para los componentes específicos
        $activeJobs = Trabajo::where('activo', true)->count();
        $acceptedApplications = Postulacion::where('estado', 'aceptado')->count();
        $rejectedApplications = Postulacion::where('estado', 'rechazado')->count();

        // Postulaciones por estado
        $applicationsByStatus = Postulacion::select('estado', DB::raw('count(*) as count'))
            ->groupBy('estado')
            ->get()
            ->pluck('count', 'estado')
            ->toArray();

        // Trabajos creados por mes (últimos 6 meses)
        $jobsByMonth = $this->getJobsByMonth();

        // Postulaciones por mes (últimos 6 meses)
        $applicationsByMonth = $this->getApplicationsByMonth();

        // Top 5 trabajos con más postulaciones
        $topJobs = Trabajo::withCount('postulaciones')
            ->orderBy('postulaciones_count', 'desc')
            ->limit(5)
            ->get();

        // Usuarios registrados por mes (últimos 6 meses)
        $usersByMonth = $this->getUsersByMonth();

        return [
            'totalJobs' => $totalJobs,
            'totalApplications' => $totalApplications,
            'totalUsers' => $totalUsers,
            'pendingApplications' => $pendingApplications,
            'activeJobs' => $activeJobs,
            'acceptedApplications' => $acceptedApplications,
            'rejectedApplications' => $rejectedApplications,
            'applicationsByStatus' => $applicationsByStatus,
            'jobsByMonth' => $jobsByMonth,
            'applicationsByMonth' => $applicationsByMonth,
            'topJobs' => $topJobs,
            'usersByMonth' => $usersByMonth
        ];
    }

    /**
     * Obtener trabajos por mes
     */
    private function getJobsByMonth(): array
    {
        return Trabajo::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => (int)$item->month,
                    'year' => (int)$item->year,
                    'count' => (int)$item->count
                ];
            })
            ->toArray();
    }

    /**
     * Obtener aplicaciones por mes
     */
    private function getApplicationsByMonth(): array
    {
        return Postulacion::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => (int)$item->month,
                    'year' => (int)$item->year,
                    'count' => (int)$item->count
                ];
            })
            ->toArray();
    }

    /**
     * Obtener usuarios por mes
     */
    private function getUsersByMonth(): array
    {
        return User::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                return [
                    'month' => (int)$item->month,
                    'year' => (int)$item->year,
                    'count' => (int)$item->count
                ];
            })
            ->toArray();
    }
} 