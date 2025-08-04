// Configuración de la API
const API_CONFIG = {
  // URL base de la API
  baseURL: '/api',
  
  // Rutas de autenticación
  auth: {
    login: '/api/auth/login',
    register: '/api/auth/register',
    logout: '/api/auth/logout',
    refresh: '/api/auth/refresh',
    me: '/api/auth/me'
  },
  
  // Headers por defecto
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  
  // Configuración de JWT
  jwt: {
    // Tiempo de expiración en minutos (debe coincidir con config/jwt.php)
    expiresIn: 60,
    
    // Tiempo antes de expirar para refrescar automáticamente (en minutos)
    refreshBeforeExpiry: 5
  }
}

export { API_CONFIG };
export default API_CONFIG; 