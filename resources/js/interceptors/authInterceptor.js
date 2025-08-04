import axios from 'axios';
import authService from '@/services/auth';
import { isPublicRoute } from '../config/publicRoutes';

axios.interceptors.request.use(
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

export default axios; 