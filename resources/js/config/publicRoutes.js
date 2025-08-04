// Rutas públicas que no requieren token de autenticación
export const PUBLIC_ROUTES = [
    '/api/auth/login',
    '/api/auth/register',
    '/api/auth/refresh',
    '/api/stats',
    '/api/jobs/recent'
];

// Función para verificar si una URL es una ruta pública
export const isPublicRoute = (url) => {
    return PUBLIC_ROUTES.some(route => url.includes(route));
};

// Función para verificar si una URL es una ruta de autenticación
export const isAuthRoute = (url) => {
    const authRoutes = ['/api/auth/login', '/api/auth/register', '/api/auth/refresh'];
    return authRoutes.some(route => url.includes(route));
}; 