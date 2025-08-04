import axios from './interceptors';
import { API_CONFIG } from '../config/api';

class JobService {
    async getJobs(params = {}) {
        try {
            const response = await axios.get('/jobs', { params });
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getJob(id) {
        try {
            const response = await axios.get(`/jobs/${id}`);
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getRecentJobs() {
        try {
            const response = await axios.get('/jobs/recent');
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async createJob(jobData) {
        try {
            const response = await axios.post('/admin/jobs', jobData);
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async updateJob(id, jobData) {
        try {
            const response = await axios.put(`/admin/jobs/${id}`, jobData);
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async deleteJob(id) {
        try {
            const response = await axios.delete(`/admin/jobs/${id}`);
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

    async getAdminJobs(params = {}) {
        try {
            const response = await axios.get('/admin/jobs', { params });
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    async getJobStats() {
        try {
            const response = await axios.get('/admin/jobs/stats');
            return response.data;
        } catch (error) {
            throw error;
        }
    }
}

export default new JobService(); 