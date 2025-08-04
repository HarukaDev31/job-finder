# Guía de Desarrollo

## 🚀 Configuración de Desarrollo

### Opción 1: Desarrollo Híbrido (Actual)
```bash
# Laravel sirve tanto la API como el frontend
php artisan serve --port=8000
npm run dev
```

**Acceso:**
- Frontend: http://localhost:8000
- API: http://localhost:8000/api

### Opción 2: Desarrollo Separado (Recomendado)
```bash
# Terminal 1: Backend (API)
npm run dev:backend

# Terminal 2: Frontend
npm run dev:frontend
```

**Acceso:**
- Frontend: http://localhost:5173
- API: http://localhost:8000/api

## 🔧 Configuración de Entornos

### Variables de Entorno
```bash
# .env
APP_URL=http://localhost:8000
VITE_APP_API_URL=http://localhost:8000/api
```

### CORS
El backend está configurado para permitir requests desde:
- http://localhost:5173 (Vite dev server)
- http://localhost:8000 (Laravel dev server)

## 📁 Estructura de Archivos

```
resources/
├── js/
│   ├── index.html          # HTML para desarrollo separado
│   ├── app.js              # Entry point de Vue
│   ├── config/
│   │   └── api.js          # Configuración de API
│   └── interceptors/
│       └── authInterceptor.js
└── views/
    └── app.blade.php       # HTML para producción
```

## 🎯 Ventajas del Desarrollo Separado

1. **Hot Module Replacement (HMR)**: Cambios instantáneos en el frontend
2. **Mejor Performance**: Vite es más rápido que Laravel Mix
3. **Debugging**: Herramientas de desarrollo más potentes
4. **Flexibilidad**: Puedes usar diferentes versiones de Node.js
5. **Escalabilidad**: Fácil separar frontend y backend en producción

## 🚀 Comandos Útiles

```bash
# Desarrollo frontend solo
npm run dev:frontend

# Desarrollo backend solo
npm run dev:backend

# Build para producción
npm run build

# Preview del build
npm run preview
```

## 🔍 Troubleshooting

### Error de CORS
Si ves errores de CORS, verifica:
1. Que CORS esté configurado en `config/cors.php`
2. Que el middleware CORS esté habilitado
3. Que las URLs estén en `allowed_origins`

### Error de Autenticación
Si hay problemas de autenticación:
1. Verifica que `withCredentials: true` esté configurado
2. Asegúrate de que el token JWT se esté enviando correctamente
3. Revisa los headers de Authorization

### Error de Rutas
Si las rutas no funcionan:
1. Verifica que Vue Router esté configurado en modo `history`
2. Asegúrate de que Laravel redirija correctamente las rutas
3. Revisa la configuración de `baseURL` en la API 