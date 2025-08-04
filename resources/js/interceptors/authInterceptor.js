import axios from 'axios';
import authService from '@/services/auth';
import { isPublicRoute } from '../config/publicRoutes';
import { API_CONFIG } from '../config/api';

const apiClient = axios.create({
    baseURL: API_CONFIG.baseURL,
    headers: API_CONFIG.headers,
    withCredentials: true
});

apiClient.interceptors.request.use(
    (config) => {
        if (!isPublicRoute(config.url)) {
            const token = authService.getToken();
            if (token) {
                config.headers.Authorization = `Bearer ${token}`;
            }
        }
        
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default apiClient; 