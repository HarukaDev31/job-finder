<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Job Finder API",
 *     description="API para el sistema de búsqueda y gestión de empleos",
 *     @OA\Contact(
 *         email="admin@jobfinder.com",
 *         name="Soporte Job Finder"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor de desarrollo"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Endpoints para autenticación y gestión de usuarios"
 * )
 * @OA\Tag(
 *     name="Trabajos",
 *     description="Endpoints para gestión de ofertas de trabajo"
 * )
 * @OA\Tag(
 *     name="Postulaciones",
 *     description="Endpoints para gestión de postulaciones"
 * )
 * @OA\Tag(
 *     name="Dashboard",
 *     description="Endpoints para estadísticas y métricas"
 * )
 * @OA\Tag(
 *     name="Administración",
 *     description="Endpoints exclusivos para administradores"
 * )
 */
class SwaggerController extends Controller
{
    //
} 