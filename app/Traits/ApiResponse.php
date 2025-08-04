<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Respuesta exitosa
     */
    protected function successResponse($data = null, string $message = 'Operación exitosa', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Si $data es un array y contiene 'token' o 'user', mantener compatibilidad
        if (is_array($data) && (isset($data['token']) || isset($data['user']))) {
            $response = array_merge($response, $data);
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta exitosa con paginación
     */
    protected function successPaginatedResponse(LengthAwarePaginator $paginator, string $message = 'Datos obtenidos exitosamente'): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'has_previous_page' => $paginator->previousPageUrl() !== null,
                'has_next_page' => $paginator->nextPageUrl() !== null,
                'previous_page_url' => $paginator->previousPageUrl(),
                'next_page_url' => $paginator->nextPageUrl(),
                'first_page_url' => $paginator->url(1),
                'last_page_url' => $paginator->url($paginator->lastPage())
            ]
        ]);
    }

    /**
     * Respuesta de error
     */
    protected function errorResponse(string $message = 'Error en la operación', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Respuesta de error de validación
     */
    protected function validationErrorResponse($errors, string $message = 'Error de validación'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }

    /**
     * Respuesta de recurso no encontrado
     */
    protected function notFoundResponse(string $message = 'Recurso no encontrado'): JsonResponse
    {
        return $this->errorResponse($message, 404);
    }

    /**
     * Respuesta de acceso denegado
     */
    protected function forbiddenResponse(string $message = 'Acceso denegado'): JsonResponse
    {
        return $this->errorResponse($message, 403);
    }

    /**
     * Respuesta de error interno del servidor
     */
    protected function serverErrorResponse(string $message = 'Error interno del servidor'): JsonResponse
    {
        return $this->errorResponse($message, 500);
    }

    /**
     * Respuesta de recurso creado
     */
    protected function createdResponse($data = null, string $message = 'Recurso creado exitosamente'): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        // Si $data es un array y contiene 'token' o 'user', mantener compatibilidad
        if (is_array($data) && (isset($data['token']) || isset($data['user']))) {
            $response = array_merge($response, $data);
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, 201);
    }

    /**
     * Respuesta de recurso actualizado
     */
    protected function updatedResponse($data = null, string $message = 'Recurso actualizado exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message, 200);
    }

    /**
     * Respuesta de recurso eliminado
     */
    protected function deletedResponse(string $message = 'Recurso eliminado exitosamente'): JsonResponse
    {
        return $this->successResponse(null, $message, 200);
    }

    /**
     * Respuesta de lista de recursos
     */
    protected function listResponse($data, string $message = 'Lista obtenida exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message);
    }



    /**
     * Respuesta de recurso individual
     */
    protected function showResponse($data, string $message = 'Recurso obtenido exitosamente'): JsonResponse
    {
        return $this->successResponse($data, $message);
    }
} 