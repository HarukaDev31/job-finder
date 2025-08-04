import axios from './interceptors';
import { API_CONFIG } from '../config/api';

class ApplicationService {
    async getMyApplications(params = {}) {
        try {
            const response = await axios.get('/applications/my', { params });
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getAllApplications(params = {}) {
        try {
            const response = await axios.get('/admin/applications', { params });
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async createApplication(applicationData) {
        try {
            const formData = new FormData();
            formData.append('trabajo_id', applicationData.trabajo_id);
            formData.append('mensaje', applicationData.mensaje);
            formData.append('cv', applicationData.cv);

            const response = await axios.post('/applications', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async updateApplicationStatus(id, statusData) {
        try {
            const response = await axios.put(`/admin/applications/${id}/status`, statusData);
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async downloadCV(applicationId) {
        try {
            const response = await axios.get(`/applications/${applicationId}/cv`, {
                responseType: 'blob'
            });
            return response;
        } catch (error) {
            throw error;
        }
    }

    async getApplicationStats() {
        try {
            const response = await axios.get('/admin/applications/stats');
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getJobApplications(jobId, params = {}) {
        try {
            const response = await axios.get(`/admin/jobs/${jobId}/applications`, { params });
            return response.data;
        } catch (error) {
            throw error;
        }
    }
}

export default new ApplicationService(); 