import axios from 'axios';
import authService from '@/services/auth';

// Interceptor para manejar errores de respuesta
axios.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        const { response } = error;
        
        // Manejar diferentes tipos de errores
        switch (response?.status) {
            case 401:
                // Token expirado o inválido - el refresh ya se maneja en auth.js
                console.warn('Error 401: No autorizado');
                break;
                
            case 403:
                // Acceso denegado
                console.error('Error 403: Acceso denegado', response.data);
                // Opcional: mostrar notificación al usuario
                break;
                
            case 404:
                // Recurso no encontrado
                console.error('Error 404: Recurso no encontrado', response.data);
                break;
                
            case 422:
                // Error de validación
                console.error('Error 422: Error de validación', response.data);
                break;
                
            case 500:
                // Error interno del servidor
                console.error('Error 500: Error interno del servidor', response.data);
                break;
                
            default:
                // Otros errores
                console.error('Error de red:', error.message);
                break;
        }
        
        return Promise.reject(error);
    }
);

export default axios; 