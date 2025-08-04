# Estructura del Proyecto Job Finder

## Backend (Laravel)

### Controladores
- `AuthController.php` - Autenticación (login, register, logout, refresh, me)
- `JobController.php` - Gestión de trabajos (CRUD)
- `ApplicationController.php` - Gestión de postulaciones
- `DashboardController.php` - Métricas del dashboard
- `ApiController.php` - Capa de compatibilidad

### Servicios
- `JobService.php` - Lógica de negocio para trabajos
- `ApplicationService.php` - Lógica de negocio para postulaciones
- `DashboardService.php` - Lógica de negocio para métricas

### Requests
- `CreateJobRequest.php` - Validación para crear trabajos
- `CreateApplicationRequest.php` - Validación para crear postulaciones
- `UpdateApplicationStatusRequest.php` - Validación para actualizar estado

### Middleware
- `AdminMiddleware.php` - Protección de rutas de admin
- `PostulanteMiddleware.php` - Protección de rutas de postulantes

### Modelos
- `User.php` - Usuarios del sistema
- `Trabajo.php` - Trabajos disponibles
- `Postulante.php` - Información de postulantes
- `Postulacion.php` - Postulaciones a trabajos

### Traits
- `ApiResponse.php` - Respuestas JSON estandarizadas

### Rutas API
```
/api/auth.php - Rutas de autenticación
/api/public.php - Rutas públicas
/api/user.php - Rutas de usuarios autenticados
/api/admin/jobs.php - Rutas de admin para trabajos
/api/admin/applications.php - Rutas de admin para postulaciones
/api/admin/dashboard.php - Rutas de admin para dashboard
```

### Seeders
- `AdminSeeder.php` - Usuarios administradores
- `PostulanteSeeder.php` - Usuarios postulantes
- `TrabajoSeeder.php` - Trabajos de prueba
- `PostulacionSeeder.php` - Postulaciones de prueba

### Tests
- `AdminRouteSecurityTest.php` - Pruebas de seguridad de rutas admin
- `AdminMiddlewareTest.php` - Pruebas del middleware de admin

## Frontend (Vue.js)

### Componentes Principales
- `App.vue` - Componente raíz con navegación
- `Home.vue` - Página de inicio pública
- `Dashboard.vue` - Dashboard de usuarios
- `Jobs.vue` - Lista de trabajos para postulantes
- `Applications.vue` - Postulaciones del usuario
- `Pagination.vue` - Componente de paginación reutilizable

### Componentes de Autenticación
- `Auth/Login.vue` - Formulario de login
- `Auth/Register.vue` - Formulario de registro

### Componentes de Admin
- `admin/AdminDashboard.vue` - Dashboard administrativo
- `admin/AdminJobs.vue` - Gestión de trabajos
- `admin/AdminApplications.vue` - Gestión de postulaciones

### Componentes de Gráficos
- `charts/LineChart.vue` - Gráfico de líneas
- `charts/BarChart.vue` - Gráfico de barras
- `charts/DoughnutChart.vue` - Gráfico de dona

### Servicios
- `services/auth.js` - Servicio de autenticación con JWT
- `config/api.js` - Configuración de API

### Mixins
- `mixins/applicationStatusMixin.js` - Lógica de estados de postulaciones

## Características Principales

### Seguridad
- ✅ Autenticación JWT
- ✅ Middleware de roles (admin/postulante)
- ✅ Validación de requests
- ✅ Pruebas unitarias de seguridad

### Arquitectura
- ✅ SOLID Principles
- ✅ Separación de responsabilidades
- ✅ Services Layer
- ✅ Request Classes
- ✅ API Response Trait

### Frontend
- ✅ Vue.js con BootstrapVue
- ✅ Componentes reutilizables
- ✅ Mixins para lógica compartida
- ✅ Paginación
- ✅ Filtros y búsqueda
- ✅ Gráficos interactivos

### Base de Datos
- ✅ Migraciones optimizadas
- ✅ Seeders con datos de prueba
- ✅ Factories para testing
- ✅ Relaciones Eloquent

### Testing
- ✅ Pruebas unitarias completas
- ✅ Pruebas de seguridad
- ✅ Cobertura de middleware
- ✅ Datos de prueba con Faker 