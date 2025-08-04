import axios from './interceptors';
import { API_CONFIG } from '../config/api';

class DashboardService {
    async getStats() {
        try {
            const response = await axios.get('/stats');
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getAdminMetrics() {
        try {
            const response = await axios.get('/admin/metrics');
            return response.data;
        } catch (error) {
            throw error;
        }
    }
}

export default new DashboardService(); 