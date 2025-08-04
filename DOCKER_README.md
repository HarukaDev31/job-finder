# Docker Setup - Job Finder

## Requisitos Previos

- Docker
- Docker Compose

## Configuración Rápida

### 1. Clonar el proyecto
```bash
git clone <repository-url>
cd job-finder
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
```

Editar `.env` con las siguientes configuraciones para Docker:
```env
APP_NAME="Job Finder"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=job_finder
DB_USERNAME=job_finder
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

JWT_SECRET=your-jwt-secret-key
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

### 3. Construir y ejecutar contenedores
```bash
docker-compose up -d --build
```

### 4. Instalar dependencias y configurar Laravel
```bash
# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Instalar dependencias de PHP
composer install

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Ejecutar seeders
php artisan db:seed

# Instalar dependencias de Node.js
npm install

# Compilar assets
npm run build

# Configurar permisos
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 5. Acceder a la aplicación
- **Frontend**: http://localhost:8000
- **API**: http://localhost:8000/api
- **Base de datos**: localhost:3306
- **Redis**: localhost:6379

## Comandos Útiles

### Gestión de contenedores
```bash
# Iniciar contenedores
docker-compose up -d

# Detener contenedores
docker-compose down

# Ver logs
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f app
docker-compose logs -f db
docker-compose logs -f webserver

# Reconstruir contenedores
docker-compose up -d --build

# Eliminar contenedores y volúmenes
docker-compose down -v
```

### Comandos dentro del contenedor
```bash
# Entrar al contenedor de la aplicación
docker-compose exec app bash

# Ejecutar comandos de Artisan
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan test

# Ejecutar comandos de Composer
docker-compose exec app composer install
docker-compose exec app composer update

# Ejecutar comandos de Node.js
docker-compose exec app npm install
docker-compose exec app npm run build
docker-compose exec app npm run dev
```

### Gestión de base de datos
```bash
# Acceder a MySQL
docker-compose exec db mysql -u job_finder -p job_finder

# Hacer backup de la base de datos
docker-compose exec db mysqldump -u job_finder -p job_finder > backup.sql

# Restaurar backup
docker-compose exec -T db mysql -u job_finder -p job_finder < backup.sql
```

## Estructura de Contenedores

### app (PHP-FPM)
- **Imagen**: php:8.2-fpm
- **Puerto**: 9000 (interno)
- **Volúmenes**: 
  - `./:/var/www` (código fuente)
  - `./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini` (configuración PHP)

### webserver (Nginx)
- **Imagen**: nginx:alpine
- **Puerto**: 8000:80
- **Volúmenes**:
  - `./:/var/www` (código fuente)
  - `./docker/nginx/conf.d/:/etc/nginx/conf.d/` (configuración Nginx)

### db (MySQL)
- **Imagen**: mysql:8.0
- **Puerto**: 3306:3306
- **Variables de entorno**:
  - `MYSQL_DATABASE=job_finder`
  - `MYSQL_USER=job_finder`
  - `MYSQL_PASSWORD=password`
  - `MYSQL_ROOT_PASSWORD=root`

### redis
- **Imagen**: redis:alpine
- **Puerto**: 6379:6379

## Configuraciones

### PHP (docker/php/local.ini)
```ini
upload_max_filesize=40M
post_max_size=40M
memory_limit=512M
max_execution_time=600
max_input_vars=3000
```

### Nginx (docker/nginx/conf.d/app.conf)
Configuración optimizada para Laravel con:
- Soporte para PHP-FPM
- Configuración de rutas
- Compresión gzip
- Headers de seguridad

### MySQL (docker/mysql/my.cnf)
```ini
[mysqld]
general_log = 1
general_log_file = /var/lib/mysql/general.log
default-authentication-plugin=mysql_native_password
```

## Desarrollo

### Modo desarrollo
```bash
# Compilar assets en modo desarrollo
docker-compose exec app npm run dev

# Ver logs en tiempo real
docker-compose logs -f
```

### Modo producción
```bash
# Compilar assets para producción
docker-compose exec app npm run build

# Optimizar Laravel
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

## Troubleshooting

### Problemas comunes

1. **Error de permisos**
```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

2. **Error de conexión a la base de datos**
```bash
# Verificar que el contenedor de DB esté corriendo
docker-compose ps

# Ver logs de la base de datos
docker-compose logs db
```

3. **Error de memoria**
```bash
# Aumentar memoria en docker/php/local.ini
memory_limit=1024M
```

4. **Error de puertos ocupados**
```bash
# Cambiar puertos en docker-compose.yml
ports:
  - "8001:80"  # Cambiar 8000 por 8001
```

### Logs útiles
```bash
# Logs de Laravel
docker-compose exec app tail -f storage/logs/laravel.log

# Logs de Nginx
docker-compose exec webserver tail -f /var/log/nginx/error.log

# Logs de MySQL
docker-compose exec db tail -f /var/lib/mysql/general.log
```

## Producción

Para producción, se recomienda:

1. Cambiar `APP_ENV=production` en `.env`
2. Configurar `APP_DEBUG=false`
3. Usar variables de entorno seguras
4. Configurar SSL/TLS
5. Implementar backup automático de la base de datos
6. Configurar monitoreo y logs centralizados 