<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Services\ApplicationService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Postulaciones",
 *     description="Endpoints para gestión de postulaciones"
 * )
 */
class ApplicationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ApplicationService $applicationService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/applications/my",
     *     operationId="getMyApplications",
     *     tags={"Postulaciones"},
     *     summary="Obtener mis postulaciones",
     *     description="Obtiene las postulaciones del usuario autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pendiente","revisando","aceptada","rechazada"}, example="pendiente")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mis postulaciones obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Mis postulaciones obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=5)
     *             )
     *         )
     *     )
     * )
     */
    public function myApplications(Request $request): JsonResponse
    {
        $filters = $request->only(['per_page', 'status']);
        $applications = $this->applicationService->getUserApplications(Auth::user(), $filters);

        return $this->successPaginatedResponse($applications, 'Mis postulaciones obtenidas exitosamente');
    }

    /**
     * @OA\Post(
     *     path="/api/applications",
     *     operationId="createApplication",
     *     tags={"Postulaciones"},
     *     summary="Crear nueva postulación",
     *     description="Crea una nueva postulación para un trabajo",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"trabajo_id","cv"},
     *                 @OA\Property(property="trabajo_id", type="integer", example=1),
     *                 @OA\Property(property="cv", type="string", format="binary", description="Archivo CV en PDF"),
     *                 @OA\Property(property="mensaje", type="string", example="Me interesa mucho esta oportunidad...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Postulación enviada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Postulación enviada exitosamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(CreateApplicationRequest $request): JsonResponse
    {
        try {
            $application = $this->applicationService->createApplication(
                Auth::user(), 
                $request->validated()
            );

            return $this->createdResponse($application, 'Postulación enviada exitosamente');

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/admin/applications/{id}/status",
     *     operationId="updateApplicationStatus",
     *     tags={"Administración"},
     *     summary="Actualizar estado de postulación",
     *     description="Actualiza el estado de una postulación (solo administradores)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la postulación",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"estado"},
     *             @OA\Property(property="estado", type="string", enum={"pendiente","revisando","aceptada","rechazada"}, example="revisando")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estado de postulación actualizado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estado de postulación actualizado correctamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Postulación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Postulación no encontrada")
     *         )
     *     )
     * )
     */
    public function updateStatus(UpdateApplicationStatusRequest $request, int $id): JsonResponse
    {
        try {
            $application = $this->applicationService->updateApplicationStatus(
                $id, 
                $request->validated()['estado']
            );

            return $this->updatedResponse($application, 'Estado de postulación actualizado correctamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al actualizar el estado de la postulación');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/jobs/{jobId}/applications",
     *     operationId="getJobApplications",
     *     tags={"Administración"},
     *     summary="Obtener postulaciones de un trabajo",
     *     description="Obtiene todas las postulaciones de un trabajo específico",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="jobId",
     *         in="path",
     *         description="ID del trabajo",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pendiente","revisando","aceptada","rechazada"}, example="pendiente")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Postulaciones del trabajo obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Postulaciones del trabajo obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=25)
     *             )
     *         )
     *     )
     * )
     */
    public function jobApplications(Request $request, int $jobId): JsonResponse
    {
        try {
            $filters = $request->only(['per_page', 'status']);
            $applications = $this->applicationService->getJobApplications($jobId, $filters);

            return $this->successPaginatedResponse($applications, 'Postulaciones del trabajo obtenidas exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al cargar las postulaciones del trabajo');
        }
    }

    /**
     * Descargar CV de una aplicación
     */
    public function downloadCV(int $applicationId)
    {
        try {
            $fileData = $this->applicationService->downloadCV($applicationId, Auth::user());

            return response()->download(
                $fileData['file_path'], 
                $fileData['download_name'], 
                [
                    'Content-Type' => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $fileData['download_name'] . '"'
                ]
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/applications",
     *     operationId="getAllApplications",
     *     tags={"Administración"},
     *     summary="Obtener todas las postulaciones",
     *     description="Obtiene todas las postulaciones del sistema (solo administradores)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filtrar por estado",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pendiente","revisando","aceptada","rechazada"}, example="pendiente")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por nombre del postulante",
     *         required=false,
     *         @OA\Schema(type="string", example="Juan Pérez")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todas las postulaciones obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Todas las postulaciones obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['per_page', 'status', 'search']);
        $applications = $this->applicationService->getAllApplications($filters);

        return $this->successPaginatedResponse($applications, 'Todas las postulaciones obtenidas exitosamente');
    }

    /**
     * @OA\Get(
     *     path="/api/admin/applications/stats",
     *     operationId="getApplicationStats",
     *     tags={"Administración"},
     *     summary="Obtener estadísticas de postulaciones",
     *     description="Obtiene estadísticas detalladas de las postulaciones",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas de postulaciones obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estadísticas de postulaciones obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_applications", type="integer", example=500),
     *                 @OA\Property(property="applications_by_status", type="object"),
     *                 @OA\Property(property="applications_by_month", type="object"),
     *                 @OA\Property(property="top_applicants", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $stats = $this->applicationService->getApplicationStats();

        return $this->successResponse($stats, 'Estadísticas de postulaciones obtenidas exitosamente');
    }
}
