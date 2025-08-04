<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Endpoints para estadísticas y métricas"
 * )
 */
class DashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        private DashboardService $dashboardService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/stats",
     *     operationId="getStats",
     *     tags={"Dashboard"},
     *     summary="Obtener estadísticas del portal",
     *     description="Obtiene estadísticas generales del portal (público)",
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas del portal obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estadísticas del portal obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_jobs", type="integer", example=150),
     *                 @OA\Property(property="active_jobs", type="integer", example=120),
     *                 @OA\Property(property="total_applications", type="integer", example=500),
     *                 @OA\Property(property="total_users", type="integer", example=200)
     *             )
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $stats = $this->dashboardService->getStats();

        return $this->successResponse($stats, 'Estadísticas del portal obtenidas exitosamente');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/metrics",
     *     operationId="getMetrics",
     *     tags={"Administración"},
     *     summary="Obtener métricas del dashboard",
     *     description="Obtiene métricas detalladas para el dashboard administrativo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Métricas del dashboard obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Métricas del dashboard obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="jobs_by_status", type="object"),
     *                 @OA\Property(property="applications_by_status", type="object"),
     *                 @OA\Property(property="recent_activity", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="top_jobs", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Acceso denegado")
     *         )
     *     )
     * )
     */
    public function metrics(): JsonResponse
    {
        $metrics = $this->dashboardService->getMetrics();

        return $this->successResponse($metrics, 'Métricas del dashboard obtenidas exitosamente');
    }
}
