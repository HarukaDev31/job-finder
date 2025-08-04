<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobRequest;
use App\Services\JobService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Trabajos",
 *     description="Endpoints para gestión de ofertas de trabajo"
 * )
 */
class JobController extends Controller
{
    use ApiResponse;

    public function __construct(
        private JobService $jobService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/jobs",
     *     operationId="getJobs",
     *     tags={"Trabajos"},
     *     summary="Obtener lista de trabajos",
     *     description="Obtiene una lista paginada de trabajos con filtros opcionales",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Número de elementos por página",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Término de búsqueda",
     *         required=false,
     *         @OA\Schema(type="string", example="desarrollador")
     *     ),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         description="Filtrar por estado activo",
     *         required=false,
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="min_salary",
     *         in="query",
     *         description="Salario mínimo",
     *         required=false,
     *         @OA\Schema(type="number", example=2000000)
     *     ),
     *     @OA\Parameter(
     *         name="max_salary",
     *         in="query",
     *         description="Salario máximo",
     *         required=false,
     *         @OA\Schema(type="number", example=5000000)
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Campo para ordenar",
     *         required=false,
     *         @OA\Schema(type="string", example="created_at")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trabajos obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trabajos obtenidos exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'per_page', 'search', 'active', 'min_salary', 'max_salary', 'sort_by'
        ]);

        $jobs = $this->jobService->getJobs($filters);

        return $this->successPaginatedResponse($jobs, 'Trabajos obtenidos exitosamente');
    }

    /**
     * @OA\Get(
     *     path="/api/jobs/{id}",
     *     operationId="getJob",
     *     tags={"Trabajos"},
     *     summary="Obtener trabajo específico",
     *     description="Obtiene la información detallada de un trabajo",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del trabajo",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trabajo obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trabajo obtenido exitosamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trabajo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Trabajo no encontrado")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $job = $this->jobService->getJob($id);

            return $this->showResponse($job, 'Trabajo obtenido exitosamente');

        } catch (\Exception $e) {
            return $this->notFoundResponse('Trabajo no encontrado');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/jobs",
     *     operationId="createJob",
     *     tags={"Administración"},
     *     summary="Crear nuevo trabajo",
     *     description="Crea una nueva oferta de trabajo (solo administradores)",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"titulo","descripcion","empresa","ubicacion","tipo_contrato","salario_min","salario_max"},
     *             @OA\Property(property="titulo", type="string", example="Desarrollador Full Stack"),
     *             @OA\Property(property="descripcion", type="string", example="Buscamos un desarrollador con experiencia en Laravel y Vue.js"),
     *             @OA\Property(property="empresa", type="string", example="TechCorp"),
     *             @OA\Property(property="ubicacion", type="string", example="Bogotá, Colombia"),
     *             @OA\Property(property="tipo_contrato", type="string", enum={"tiempo_completo","tiempo_parcial","contrato","freelance"}, example="tiempo_completo"),
     *             @OA\Property(property="salario_min", type="number", example=3000000),
     *             @OA\Property(property="salario_max", type="number", example=5000000),
     *             @OA\Property(property="requisitos", type="array", @OA\Items(type="string"), example={"Laravel", "Vue.js", "MySQL"}),
     *             @OA\Property(property="beneficios", type="array", @OA\Items(type="string"), example={"Seguro médico", "Bonos de productividad"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trabajo creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trabajo creado exitosamente"),
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
    public function store(CreateJobRequest $request): JsonResponse
    {
        try {
            $job = $this->jobService->createJob($request->validated());

            return $this->createdResponse($job, 'Trabajo creado exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al crear el trabajo');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/jobs/recent",
     *     operationId="getRecentJobs",
     *     tags={"Trabajos"},
     *     summary="Obtener trabajos recientes",
     *     description="Obtiene una lista de trabajos recientes (público)",
     *     @OA\Response(
     *         response=200,
     *         description="Trabajos recientes obtenidos exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trabajos recientes obtenidos exitosamente"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function recent(): JsonResponse
    {
        $jobs = $this->jobService->getRecentJobs();

        return $this->listResponse($jobs, 'Trabajos recientes obtenidos exitosamente');
    }

    /**
     * @OA\Put(
     *     path="/api/admin/jobs/{id}",
     *     operationId="updateJob",
     *     tags={"Administración"},
     *     summary="Actualizar trabajo",
     *     description="Actualiza la información de un trabajo existente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del trabajo",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="titulo", type="string", example="Desarrollador Full Stack Senior"),
     *             @OA\Property(property="descripcion", type="string", example="Buscamos un desarrollador senior con experiencia en Laravel y Vue.js"),
     *             @OA\Property(property="empresa", type="string", example="TechCorp"),
     *             @OA\Property(property="ubicacion", type="string", example="Bogotá, Colombia"),
     *             @OA\Property(property="tipo_contrato", type="string", enum={"tiempo_completo","tiempo_parcial","contrato","freelance"}, example="tiempo_completo"),
     *             @OA\Property(property="salario_min", type="number", example=4000000),
     *             @OA\Property(property="salario_max", type="number", example=6000000),
     *             @OA\Property(property="requisitos", type="array", @OA\Items(type="string"), example={"Laravel", "Vue.js", "MySQL", "5+ años experiencia"}),
     *             @OA\Property(property="beneficios", type="array", @OA\Items(type="string"), example={"Seguro médico", "Bonos de productividad", "Home office"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trabajo actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trabajo actualizado exitosamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trabajo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Trabajo no encontrado")
     *         )
     *     )
     * )
     */
    public function update(CreateJobRequest $request, int $id): JsonResponse
    {
        try {
            $job = $this->jobService->updateJob($id, $request->validated());

            return $this->updatedResponse($job, 'Trabajo actualizado exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al actualizar el trabajo');
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/jobs/{id}",
     *     operationId="deleteJob",
     *     tags={"Administración"},
     *     summary="Eliminar trabajo",
     *     description="Elimina un trabajo existente",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del trabajo",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trabajo eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Trabajo eliminado exitosamente"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trabajo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Trabajo no encontrado")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->jobService->deleteJob($id);

            return $this->successResponse(null, 'Trabajo eliminado exitosamente');

        } catch (\Exception $e) {
            return $this->serverErrorResponse('Error al eliminar el trabajo');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/jobs/stats",
     *     operationId="getJobStats",
     *     tags={"Administración"},
     *     summary="Obtener estadísticas de trabajos",
     *     description="Obtiene estadísticas detalladas de los trabajos",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Estadísticas de trabajos obtenidas exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Estadísticas de trabajos obtenidas exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="total_jobs", type="integer", example=150),
     *                 @OA\Property(property="active_jobs", type="integer", example=120),
     *                 @OA\Property(property="inactive_jobs", type="integer", example=30),
     *                 @OA\Property(property="jobs_by_type", type="object"),
     *                 @OA\Property(property="jobs_by_location", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function stats(): JsonResponse
    {
        $stats = $this->jobService->getJobStats();

        return $this->successResponse($stats, 'Estadísticas de trabajos obtenidas exitosamente');
    }
}
