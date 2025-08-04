# Job Finder - Sistema de Gestión de Empleos

Un sistema completo de gestión de empleos desarrollado con Laravel y Vue.js, que permite a los usuarios postular a trabajos y a los administradores gestionar ofertas laborales y postulaciones.

## 🚀 Características Principales

### Para Usuarios (Postulantes)
- ✅ Registro e inicio de sesión con JWT
- ✅ Búsqueda y filtrado de trabajos
- ✅ Postulación a trabajos con CV
- ✅ Seguimiento de postulaciones
- ✅ Dashboard personal

### Para Administradores
- ✅ Gestión completa de trabajos (CRUD)
- ✅ Gestión de postulaciones
- ✅ Dashboard con métricas y gráficos
- ✅ Cambio de estados de postulaciones
- ✅ Descarga de CVs

### Características Técnicas
- ✅ API RESTful con Laravel
- ✅ Frontend con Vue.js y BootstrapVue
- ✅ Autenticación JWT
- ✅ Paginación y filtros
- ✅ Gráficos interactivos
- ✅ Subida y descarga de archivos
- ✅ Pruebas unitarias completas
- ✅ Dockerizado
- ✅ Documentación API completa

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel 10** - Framework PHP
- **MySQL 8.0** - Base de datos
- **Redis** - Cache y sesiones
- **JWT** - Autenticación
- **PHPUnit** - Testing

### Frontend
- **Vue.js 3** - Framework JavaScript
- **BootstrapVue** - UI Components
- **Vue-Chartjs** - Gráficos
- **Vee-Validate** - Validación de formularios
- **Axios** - Cliente HTTP

### DevOps
- **Docker** - Contenedores
- **Docker Compose** - Orquestación
- **Nginx** - Servidor web
- **PHP-FPM** - Procesador PHP

## 📋 Requisitos

- PHP 8.2+
- Node.js 16+
- MySQL 8.0+
- Redis
- Docker (opcional)

## 🚀 Instalación

### Opción 1: Instalación Local

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd job-finder
```

2. **Instalar dependencias**
```bash
composer install
npm install
```

3. **Configurar variables de entorno**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar base de datos**
```bash
php artisan migrate
php artisan db:seed
```

5. **Compilar assets**
```bash
npm run build
```

6. **Iniciar servidor**
```bash
php artisan serve
```

### Opción 2: Docker (Recomendado)

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd job-finder
```

2. **Configurar variables de entorno**
```bash
cp .env.example .env
```

3. **Construir y ejecutar contenedores**
```bash
docker-compose up -d --build
```

4. **Configurar Laravel**
```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app npm install
docker-compose exec app npm run build
```

5. **Acceder a la aplicación**
- Frontend: http://localhost:8000
- API: http://localhost:8000/api

## 📚 Documentación

- [API Documentation](API_DOCUMENTATION.md) - Documentación completa de la API
- [Docker Setup](DOCKER_README.md) - Guía de instalación con Docker
- [Project Structure](PROJECT_STRUCTURE.md) - Estructura del proyecto

## 🧪 Testing

Ejecutar todas las pruebas:
```bash
php artisan test
```

Ejecutar pruebas específicas:
```bash
php artisan test tests/Feature/AdminRouteSecurityTest.php
php artisan test tests/Feature/AdminMiddlewareTest.php
```

## 📊 Estructura del Proyecto

```
job-finder/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores de la API
│   │   ├── Requests/        # Clases de validación
│   │   └── Middleware/      # Middleware de autenticación
│   ├── Services/           # Lógica de negocio
│   ├── Models/             # Modelos Eloquent
│   └── Traits/             # Traits reutilizables
├── resources/js/
│   ├── components/         # Componentes Vue.js
│   ├── services/          # Servicios de API
│   └── mixins/            # Mixins de Vue
├── routes/api/            # Rutas de la API
├── database/
│   ├── migrations/        # Migraciones
│   ├── seeders/          # Seeders
│   └── factories/        # Factories para testing
├── tests/                # Pruebas unitarias
└── docker/               # Configuración de Docker
```

## 🔐 Seguridad

- Autenticación JWT
- Middleware de roles (admin/postulante)
- Validación de requests
- Protección CSRF
- Sanitización de datos
- Pruebas de seguridad automatizadas

## 📈 Características Avanzadas

### Arquitectura
- **SOLID Principles** - Separación de responsabilidades
- **Service Layer** - Lógica de negocio separada
- **Request Classes** - Validación centralizada
- **API Response Trait** - Respuestas estandarizadas

### Frontend
- **Componentes Reutilizables** - Paginación, gráficos, etc.
- **Mixins** - Lógica compartida
- **Servicios** - Separación de peticiones HTTP
- **Validación en Tiempo Real** - Vee-Validate

### Base de Datos
- **Migraciones Optimizadas** - Estructura eficiente
- **Seeders con Faker** - Datos de prueba realistas
- **Relaciones Eloquent** - Consultas optimizadas
- **Factories** - Datos de prueba para testing

## 🚀 Despliegue

### Producción con Docker
```bash
# Construir imagen de producción
docker build -t job-finder:production .

# Ejecutar con variables de producción
docker-compose -f docker-compose.prod.yml up -d
```

### Configuración de Producción
- Configurar variables de entorno seguras
- Habilitar SSL/TLS
- Configurar backup automático
- Implementar monitoreo
- Optimizar Laravel (cache, routes, views)

## 🤝 Contribución

1. Fork el proyecto
2. Crear una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte técnico o preguntas:
- Crear un issue en GitHub
- Contactar al equipo de desarrollo

## 🎯 Roadmap

- [ ] Notificaciones en tiempo real
- [ ] Sistema de mensajería interno
- [ ] Integración con redes sociales
- [ ] API móvil nativa
- [ ] Sistema de recomendaciones
- [ ] Análisis avanzado de datos
- [ ] Integración con LinkedIn
- [ ] Sistema de calificaciones
