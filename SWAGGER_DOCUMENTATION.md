# Documentación de API - Job Finder

## 📚 Descripción

Esta documentación describe la API REST del sistema Job Finder, un portal de búsqueda y gestión de empleos desarrollado con Laravel y documentado con Swagger/OpenAPI.

## 🚀 Acceso a la Documentación

### URL de la Documentación
```
http://localhost:8000/api/documentation
```


## 🔐 Autenticación

La API utiliza autenticación JWT (JSON Web Tokens). Para acceder a endpoints protegidos:

1. **Registrarse**: `POST /api/auth/register`
2. **Iniciar sesión**: `POST /api/auth/login`
3. **Usar el token**: Incluir en el header: `Authorization: Bearer {token}`

## 📋 Endpoints Disponibles

### 🔑 Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/register` - Registrar nuevo postulante
- `GET /api/auth/me` - Obtener usuario autenticado
- `POST /api/auth/logout` - Cerrar sesión
- `POST /api/auth/refresh` - Refrescar token

### 💼 Trabajos
- `GET /api/jobs` - Listar trabajos (con filtros)
- `GET /api/jobs/{id}` - Obtener trabajo específico
- `GET /api/jobs/recent` - Trabajos recientes (público)

### 📝 Postulaciones
- `GET /api/applications/my` - Mis postulaciones
- `POST /api/applications` - Crear nueva postulación
- `GET /api/applications/{id}/cv` - Descargar CV

### 📊 Dashboard
- `GET /api/stats` - Estadísticas del portal (público)

### 👨‍💼 Administración (Solo Admin)
- `GET /api/admin/jobs` - Listar todos los trabajos
- `POST /api/admin/jobs` - Crear nuevo trabajo
- `PUT /api/admin/jobs/{id}` - Actualizar trabajo
- `DELETE /api/admin/jobs/{id}` - Eliminar trabajo
- `GET /api/admin/applications` - Listar todas las postulaciones
- `PUT /api/admin/applications/{id}/status` - Actualizar estado de postulación
- `GET /api/admin/jobs/{jobId}/applications` - Postulaciones de un trabajo
- `GET /api/admin/applications/stats` - Estadísticas de postulaciones
- `GET /api/admin/metrics` - Métricas del dashboard

## 🛠️ Configuración

### Requisitos
- Laravel 10+
- PHP 8.1+
- L5-Swagger package

### Instalación
```bash
# Instalar dependencias
composer install

# Publicar configuración de Swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# Generar documentación
php artisan l5-swagger:generate
```

### Configuración del Servidor
```bash
# Iniciar servidor de desarrollo
php artisan serve

# O usar Laravel Sail
./vendor/bin/sail up
```

## 📖 Uso de la Documentación

### 1. Acceder a Swagger UI
- Abrir el navegador en: `http://localhost:8000/api/documentation`
- Verás una interfaz interactiva con todos los endpoints

### 2. Autenticación en Swagger
1. Hacer clic en el botón "Authorize" (🔒)
2. Ingresar el token JWT en el formato: `Bearer {tu_token}`
3. Hacer clic en "Authorize"

### 3. Probar Endpoints
1. Seleccionar el endpoint deseado
2. Hacer clic en "Try it out"
3. Llenar los parámetros requeridos
4. Hacer clic en "Execute"

## 🔧 Personalización

### Modificar la Documentación
Los archivos de documentación se encuentran en:
- **Controladores**: `app/Http/Controllers/`
- **Configuración**: `config/l5-swagger.php`
- **Documentación generada**: `storage/api-docs/`

### Regenerar Documentación
```bash
php artisan l5-swagger:generate
```

## 📝 Estructura de Respuestas

Todas las respuestas siguen un formato estándar:

```json
{
  "success": true,
  "message": "Mensaje descriptivo",
  "data": {
    // Datos de la respuesta
  }
}
```

### Códigos de Estado HTTP
- `200` - Éxito
- `201` - Creado
- `400` - Error de validación
- `401` - No autorizado
- `403` - Prohibido
- `404` - No encontrado
- `422` - Error de validación
- `500` - Error del servidor

## 🚨 Notas Importantes

1. **Autenticación**: La mayoría de endpoints requieren autenticación JWT
2. **Roles**: Los endpoints de administración requieren rol de admin
3. **Archivos**: Los CVs se suben como archivos PDF
4. **Paginación**: Las listas incluyen paginación automática
5. **Filtros**: Muchos endpoints soportan filtros opcionales

## 📞 Soporte

Para soporte técnico o preguntas sobre la API:
- Email: admin@jobfinder.com
- Documentación: `/api/documentation`

---

**Desarrollado con ❤️ usando Laravel y Swagger** 