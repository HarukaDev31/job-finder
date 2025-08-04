<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth('api')->user();
            
            if (!$user || !$user->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado. Se requieren permisos de administrador.',
                    'error' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            
            return $next($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de autenticación',
                'error' => 'AUTHENTICATION_ERROR'
            ], 401);
        }
    }
}
