<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterPostulanteRequest;
use App\Models\Postulante;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * @OA\Tag(
 *     name="Autenticación",
 *     description="Endpoints para autenticación y gestión de usuarios"
 * )
 */
class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="login",
     *     tags={"Autenticación"},
     *     summary="Iniciar sesión",
     *     description="Autentica un usuario y devuelve un token JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="usuario@ejemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Inicio de sesión exitoso"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Credenciales inválidas"),
     *             @OA\Property(property="errors", type="object")
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors(), 'Error de validación');
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Credenciales inválidas', 401, [
                    'email' => ['Las credenciales proporcionadas no coinciden con nuestros registros.']
                ]);
            }

            $user = auth()->user();
            
            return $this->successResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ], 'Inicio de sesión exitoso');

        } catch (JWTException $e) {
            return $this->serverErrorResponse('Error al generar el token de acceso');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="register",
     *     tags={"Autenticación"},
     *     summary="Registrar nuevo postulante",
     *     description="Crea una nueva cuenta de postulante",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombres","apellidos","email","password","numero_documento","tipo_documento","fecha_nacimiento"},
     *             @OA\Property(property="nombres", type="string", example="Juan"),
     *             @OA\Property(property="apellidos", type="string", example="Pérez"),
     *             @OA\Property(property="email", type="string", format="email", example="juan.perez@ejemplo.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *             @OA\Property(property="numero_documento", type="string", example="12345678"),
     *             @OA\Property(property="tipo_documento", type="string", enum={"CC","CE","TI","PP"}, example="CC"),
     *             @OA\Property(property="fecha_nacimiento", type="string", format="date", example="1990-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registro exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registro exitoso. Bienvenido al portal de trabajo."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación o email duplicado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="El email ya está registrado"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(RegisterPostulanteRequest $request)
    {
        try {
            DB::beginTransaction();

            // Verificar si el email ya existe
            if (User::where('email', $request->email)->exists()) {
                return $this->errorResponse('El email ya está registrado', 422, [
                    'email' => ['Este email ya está siendo utilizado por otro usuario.']
                ]);
            }

            // Crear usuario
            $user = User::create([
                'name' => $request->nombres . ' ' . $request->apellidos,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'postulante',
            ]);

            // Crear postulante
            Postulante::create([
                'user_id' => $user->id,
                'numero_documento' => $request->numero_documento,
                'tipo_documento' => $request->tipo_documento,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'fecha_nacimiento' => $request->fecha_nacimiento,
            ]);

            DB::commit();

            // Generar token JWT
            $token = JWTAuth::fromUser($user);

            return $this->createdResponse([
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ], 'Registro exitoso. Bienvenido al portal de trabajo.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error en registro: ' . $e->getMessage());
            
            return $this->serverErrorResponse('Error al registrar. Por favor, intente nuevamente.');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     operationId="me",
     *     tags={"Autenticación"},
     *     summary="Obtener usuario autenticado",
     *     description="Devuelve la información del usuario autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Usuario obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Usuario obtenido exitosamente"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Token inválido",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Token inválido")
     *         )
     *     )
     * )
     */
    public function me()
    {
        try {
            $user = auth()->user();
            
            return $this->showResponse($user, 'Usuario obtenido exitosamente');
        } catch (JWTException $e) {
            return $this->errorResponse('Token inválido', 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     operationId="logout",
     *     tags={"Autenticación"},
     *     summary="Cerrar sesión",
     *     description="Invalida el token JWT del usuario",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Sesión cerrada exitosamente"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al cerrar sesión",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error al cerrar sesión")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            auth()->logout();

            return $this->successResponse(null, 'Sesión cerrada exitosamente');
        } catch (JWTException $e) {
            return $this->serverErrorResponse('Error al cerrar sesión');
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     operationId="refresh",
     *     tags={"Autenticación"},
     *     summary="Refrescar token",
     *     description="Genera un nuevo token JWT",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refrescado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Token refrescado exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No se pudo refrescar el token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="No se pudo refrescar el token")
     *         )
     *     )
     * )
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            
            return $this->successResponse([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60
            ], 'Token refrescado exitosamente');
        } catch (JWTException $e) {
            return $this->errorResponse('No se pudo refrescar el token', 401);
        }
    }
}
