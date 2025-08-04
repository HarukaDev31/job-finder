# Interceptores de Axios

Este directorio contiene los interceptores de Axios que manejan automáticamente la autenticación y el manejo de errores en toda la aplicación.

## Archivos

### `authInterceptor.js`
Interceptor que agrega automáticamente el token JWT a todas las peticiones HTTP que requieren autenticación.

**Funcionalidades:**
- Agrega el header `Authorization: Bearer {token}` a las peticiones
- Excluye rutas públicas (login, register, stats, etc.)
- Utiliza el servicio de autenticación para obtener el token

**Rutas públicas (no requieren token):**
- `/api/auth/login`
- `/api/auth/register`
- `/api/auth/refresh`
- `/api/stats`
- `/api/jobs/recent`

### `errorInterceptor.js`
Interceptor que maneja errores de respuesta de manera centralizada.

**Funcionalidades:**
- Maneja errores HTTP comunes (401, 403, 404, 422, 500)
- Registra errores en la consola para debugging
- Permite manejo personalizado de errores específicos

## Configuración

### `publicRoutes.js`
Archivo de configuración que define las rutas públicas y funciones de utilidad.

**Funciones:**
- `isPublicRoute(url)`: Verifica si una URL es una ruta pública
- `isAuthRoute(url)`: Verifica si una URL es una ruta de autenticación

## Uso

Los interceptores se cargan automáticamente al importar la aplicación:

```javascript
// En app.js
import './interceptors/authInterceptor';
import './interceptors/errorInterceptor';
```

### Ejemplo de uso en servicios

```javascript
import axios from 'axios';

// El token se agrega automáticamente
const response = await axios.get('/api/jobs');
```

### Manejo de errores

Los errores se manejan automáticamente según su código de estado:

```javascript
try {
    const response = await axios.get('/api/admin/jobs');
} catch (error) {
    // El error ya fue manejado por el interceptor
    // Aquí puedes agregar lógica específica del componente
    console.log('Error específico del componente:', error);
}
```

## Personalización

### Agregar nuevas rutas públicas

Edita `config/publicRoutes.js`:

```javascript
export const PUBLIC_ROUTES = [
    '/api/auth/login',
    '/api/auth/register',
    '/api/auth/refresh',
    '/api/stats',
    '/api/jobs/recent',
    '/api/nueva-ruta-publica' // Agregar aquí
];
```

### Personalizar manejo de errores

Edita `errorInterceptor.js` para agregar lógica específica:

```javascript
case 403:
    // Agregar lógica personalizada aquí
    console.error('Error 403: Acceso denegado', response.data);
    // Mostrar notificación al usuario
    break;
```

## Notas importantes

1. **No duplicar funcionalidad**: Los interceptores trabajan en conjunto con el servicio de autenticación (`auth.js`)
2. **Refresh automático**: El refresh de tokens se maneja en `auth.js`, no en los interceptores
3. **Orden de ejecución**: Los interceptores se ejecutan en el orden de importación
4. **Debugging**: Los errores se registran en la consola para facilitar el debugging

## Compatibilidad

Los interceptores son compatibles con:
- Vue.js 3
- Axios
- JWT tokens
- Todas las rutas de la API 