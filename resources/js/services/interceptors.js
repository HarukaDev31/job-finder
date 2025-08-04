import axios from 'axios';
import { API_CONFIG } from '../config/api';

// Crear instancia de axios con configuración base
const axiosInstance = axios.create({
    baseURL: API_CONFIG.baseURL,
    timeout: 10000,
    headers: API_CONFIG.headers
});

// Interceptor para agregar token JWT a las peticiones
axiosInstance.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('jwt_token');
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Interceptor para manejar respuestas y errores
axiosInstance.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        // Si el token ha expirado (401), redirigir al login
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('jwt_token');
            localStorage.removeItem('user');
            
            // Redirigir al login si estamos en una página que requiere autenticación
            if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
                window.location.href = '/login';
            }
        }
        
        return Promise.reject(error);
    }
);

export default axiosInstance; 