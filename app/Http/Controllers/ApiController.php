<?php

namespace App\Http\Controllers;

use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function __construct(
        private JobController $jobController,
        private ApplicationController $applicationController,
        private DashboardController $dashboardController
    ) {}

    /**
     * Obtener estadísticas del portal
     */
    public function getStats()
    {
        return $this->dashboardController->stats();
    }

    /**
     * Obtener trabajos recientes
     */
    public function getRecentJobs()
    {
        return $this->jobController->recent();
    }

    /**
     * Obtener todos los trabajos con paginación
     */
    public function getJobs(Request $request)
    {
        return $this->jobController->index($request);
    }

    /**
     * Obtener un trabajo específico
     */
    public function getJob($id)
    {
        return $this->jobController->show($id);
    }

    /**
     * Obtener mis postulaciones con paginación
     */
    public function getMyApplications(Request $request)
    {
        return $this->applicationController->myApplications($request);
    }

    /**
     * Crear una nueva postulación
     */
    public function createApplication(Request $request)
    {
        return $this->applicationController->store($request);
    }

    /**
     * Actualizar el estado de una postulación
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        return $this->applicationController->updateStatus($request, $id);
    }

    /**
     * Obtener postulaciones de un trabajo específico
     */
    public function getJobApplications(Request $request, $jobId)
    {
        return $this->applicationController->jobApplications($request, $jobId);
    }

    /**
     * Descargar CV de una aplicación
     */
    public function downloadCV(Request $request, $applicationId)
    {
        return $this->applicationController->downloadCV($applicationId);
    }

    /**
     * Obtener trabajos para administrador con paginación
     */
    public function getAdminJobs(Request $request)
    {
        return $this->jobController->index($request);
    }

    /**
     * Crear un nuevo trabajo
     */
    public function createJob(Request $request)
    {
        return $this->jobController->store($request);
    }

    /**
     * Obtener todas las postulaciones para administrador con paginación
     */
    public function getAllApplications(Request $request)
    {
        return $this->applicationController->index($request);
    }

    /**
     * Obtener métricas del dashboard para administrador
     */
    public function getMetrics()
    {
        return $this->dashboardController->metrics();
    }
} 