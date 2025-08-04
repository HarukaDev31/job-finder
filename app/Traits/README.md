# ApiResponse Trait

## Descripción

El trait `ApiResponse` proporciona métodos estandarizados para generar respuestas JSON consistentes en toda la API. Esto asegura que todas las respuestas sigan la misma estructura y formato.

## Estructura de Respuesta

### Respuesta Exitosa
```json
{
    "success": true,
    "message": "Mensaje descriptivo",
    "data": {...}
}
```

### Respuesta de Error
```json
{
    "success": false,
    "message": "Mensaje de error",
    "errors": {...} // Opcional
}
```

### Respuesta Paginada
```json
{
    "success": true,
    "message": "Datos obtenidos exitosamente",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75,
        "from": 1,
        "to": 15,
        "has_more_pages": true,
        "has_previous_page": false,
        "has_next_page": true,
        "previous_page_url": null,
        "next_page_url": "http://...",
        "first_page_url": "http://...",
        "last_page_url": "http://..."
    }
}
```

## Métodos Disponibles

### Respuestas Exitosas

#### `successResponse($data, $message, $code)`
Respuesta exitosa básica.

```php
return $this->successResponse($user, 'Usuario obtenido exitosamente');
```

#### `successPaginatedResponse($paginator, $message)`
Respuesta exitosa con paginación.

```php
return $this->successPaginatedResponse($jobs, 'Trabajos obtenidos exitosamente');
```

#### `createdResponse($data, $message)`
Respuesta para recursos creados (código 201).

```php
return $this->createdResponse($job, 'Trabajo creado exitosamente');
```

#### `updatedResponse($data, $message)`
Respuesta para recursos actualizados.

```php
return $this->updatedResponse($application, 'Estado actualizado correctamente');
```

#### `deletedResponse($message)`
Respuesta para recursos eliminados.

```php
return $this->deletedResponse('Trabajo eliminado exitosamente');
```

#### `listResponse($data, $message)`
Respuesta para listas de recursos.

```php
return $this->listResponse($jobs, 'Trabajos recientes obtenidos exitosamente');
```

#### `showResponse($data, $message)`
Respuesta para recursos individuales.

```php
return $this->showResponse($job, 'Trabajo obtenido exitosamente');
```

### Respuestas de Error

#### `errorResponse($message, $code, $errors)`
Respuesta de error básica.

```php
return $this->errorResponse('Error en la operación', 400);
```

#### `validationErrorResponse($errors, $message)`
Respuesta para errores de validación (código 422).

```php
return $this->validationErrorResponse($validator->errors(), 'Error de validación');
```

#### `notFoundResponse($message)`
Respuesta para recursos no encontrados (código 404).

```php
return $this->notFoundResponse('Trabajo no encontrado');
```

#### `forbiddenResponse($message)`
Respuesta para acceso denegado (código 403).

```php
return $this->forbiddenResponse('No tienes permisos para esta acción');
```

#### `serverErrorResponse($message)`
Respuesta para errores internos del servidor (código 500).

```php
return $this->serverErrorResponse('Error interno del servidor');
```

## Uso en Controladores

### 1. Importar el Trait
```php
use App\Traits\ApiResponse;

class JobController extends Controller
{
    use ApiResponse;
    
    // ...
}
```

### 2. Usar los Métodos
```php
public function index(Request $request): JsonResponse
{
    try {
        $jobs = $this->jobService->getJobs($request->all());
        return $this->successPaginatedResponse($jobs, 'Trabajos obtenidos exitosamente');
    } catch (\Exception $e) {
        return $this->serverErrorResponse('Error al obtener los trabajos');
    }
}

public function store(CreateJobRequest $request): JsonResponse
{
    try {
        $job = $this->jobService->createJob($request->validated());
        return $this->createdResponse($job, 'Trabajo creado exitosamente');
    } catch (\Exception $e) {
        return $this->serverErrorResponse('Error al crear el trabajo');
    }
}

public function show(int $id): JsonResponse
{
    try {
        $job = $this->jobService->getJob($id);
        return $this->showResponse($job, 'Trabajo obtenido exitosamente');
    } catch (\Exception $e) {
        return $this->notFoundResponse('Trabajo no encontrado');
    }
}
```

## Beneficios

### 1. Consistencia
- Todas las respuestas siguen la misma estructura
- Códigos de estado HTTP apropiados
- Mensajes descriptivos y consistentes

### 2. Mantenibilidad
- Cambios centralizados en un solo lugar
- Fácil modificar el formato de respuesta
- Reducción de código duplicado

### 3. Legibilidad
- Código más limpio y expresivo
- Intención clara en cada respuesta
- Fácil entender qué tipo de respuesta se está generando

### 4. Testing
- Fácil mockear respuestas
- Tests más específicos
- Validación de estructura de respuesta

## Códigos de Estado HTTP

- **200**: OK - Operación exitosa
- **201**: Created - Recurso creado
- **400**: Bad Request - Error en la solicitud
- **401**: Unauthorized - No autenticado
- **403**: Forbidden - Acceso denegado
- **404**: Not Found - Recurso no encontrado
- **422**: Unprocessable Entity - Error de validación
- **500**: Internal Server Error - Error del servidor

## Próximos Pasos

1. **Interfaces**: Crear interfaces para diferentes tipos de respuesta
2. **Resources**: Implementar Laravel Resources para transformación de datos
3. **Cache**: Agregar soporte para respuestas cacheadas
4. **Logging**: Integrar logging automático de respuestas
5. **Validation**: Agregar validación de estructura de respuesta 