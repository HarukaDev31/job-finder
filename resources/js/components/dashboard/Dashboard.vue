<template>
  <div class="dashboard">
    <!-- Welcome Section -->
    <b-row class="mb-4">
      <b-col>
        <b-card bg-variant="light" border-variant="success">
          <b-card-body class="text-center">
            <i class="fas fa-user-tie fa-3x text-success mb-3"></i>
            <h3>¡Bienvenido, {{ userName }}!</h3>
            <p class="lead">Tu centro de control para encontrar el trabajo ideal</p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Quick Stats -->
    <b-row class="mb-4">
      <b-col md="3">
        <b-card bg-variant="primary" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-briefcase fa-2x mb-2"></i>
            <h4>{{ stats.totalJobs || 0 }}</h4>
            <p class="mb-0">Trabajos Disponibles</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="success" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-paper-plane fa-2x mb-2"></i>
            <h4>{{ stats.myApplications || 0 }}</h4>
            <p class="mb-0">Mis Postulaciones</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="warning" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-clock fa-2x mb-2"></i>
            <h4>{{ stats.pendingApplications || 0 }}</h4>
            <p class="mb-0">Pendientes</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="info" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-calendar-alt fa-2x mb-2"></i>
            <h4>{{ stats.recentApplications || 0 }}</h4>
            <p class="mb-0">Esta Semana</p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Quick Actions -->
    <b-row class="mb-4">
      <b-col md="6">
        <b-card class="h-100 action-card">
          <b-card-body class="text-center">
            <i class="fas fa-search fa-3x text-primary mb-3"></i>
            <h4>Buscar Trabajos</h4>
            <p class="card-text">
              Explora cientos de ofertas de trabajo disponibles. 
              Filtra por sueldo, ubicación y tipo de contrato.
            </p>
            <b-button variant="primary" size="lg" @click="goToJobs">
              <i class="fas fa-search mr-2"></i>
              Buscar Trabajos
            </b-button>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="6">
        <b-card class="h-100 action-card">
          <b-card-body class="text-center">
            <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
            <h4>Mis Postulaciones</h4>
            <p class="card-text">
              Revisa el estado de todas tus postulaciones. 
              Mantente al día con las respuestas de las empresas.
            </p>
            <b-button variant="success" size="lg" @click="goToApplications">
              <i class="fas fa-paper-plane mr-2"></i>
              Ver Postulaciones
            </b-button>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Recent Activity -->
    <b-row>
      <b-col>
        <b-card>
          <b-card-header>
            <h5 class="mb-0">
              <i class="fas fa-history mr-2"></i>
              Actividad Reciente
            </h5>
          </b-card-header>
          <b-card-body>
            <div v-if="loading" class="text-center py-4">
              <b-spinner variant="primary" label="Cargando actividad..."></b-spinner>
              <p class="mt-3">Cargando tu actividad reciente...</p>
            </div>

            <div v-else-if="recentActivity.length === 0" class="text-center py-4">
              <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
              <p class="text-muted">No hay actividad reciente.</p>
              <b-button variant="primary" @click="goToJobs">
                <i class="fas fa-search mr-1"></i>
                Buscar tu primer trabajo
              </b-button>
            </div>

            <div v-else>
              <div v-for="activity in recentActivity" :key="activity.id" class="activity-item">
                <div class="d-flex align-items-center">
                  <div class="activity-icon mr-3">
                    <i :class="getActivityIcon(activity.type)" :style="{ color: getActivityColor(activity.type) }"></i>
                  </div>
                  <div class="activity-content flex-grow-1">
                    <h6 class="mb-1">{{ activity.title }}</h6>
                    <p class="text-muted mb-1">{{ activity.description }}</p>
                    <small class="text-muted">{{ formatDate(activity.created_at) }}</small>
                  </div>
                  <div class="activity-action">
                    <b-button variant="outline-primary" size="sm" @click="handleActivityAction(activity)">
                      Ver
                    </b-button>
                  </div>
                </div>
                <hr v-if="activity !== recentActivity[recentActivity.length - 1]">
              </div>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import authService from '@/services/auth.js'
import applicationStatusMixin from '@/mixins/applicationStatusMixin.js'
import dashboardService from '@/services/dashboardService'
import jobService from '@/services/jobService'
import applicationService from '@/services/applicationService'

export default {
  name: 'Dashboard',
  mixins: [applicationStatusMixin],
  data() {
    return {
      loading: false,
      stats: {
        totalJobs: 0,
        myApplications: 0,
        pendingApplications: 0,
        recentApplications: 0
      },
      recentActivity: []
    }
  },
  computed: {
    user() {
      return authService.getUser()
    },
    userName() {
      return this.user ? this.user.name : 'Usuario'
    }
  },
  mounted() {
    this.loadDashboardData()
  },
  methods: {
    async loadDashboardData() {
      this.loading = true
      try {
        await Promise.all([
          this.loadStats(),
          this.loadRecentActivity()
        ])
      } catch (error) {
        console.error('Error loading dashboard data:', error)
        this.$bvToast.toast('Error al cargar los datos del dashboard', {
          title: 'Error',
          variant: 'danger',
          solid: true
        })
      } finally {
        this.loading = false
      }
    },
    async loadStats() {
      try {
        const [statsResponse, applicationsResponse] = await Promise.all([
          dashboardService.getStats(),
          applicationService.getMyApplications({ per_page: 1 }) // Solo para obtener el total
        ])
        
        this.stats.totalJobs = statsResponse.data.jobs || 0
        this.stats.myApplications = applicationsResponse.pagination?.total || 0
        
        // Calcular aplicaciones pendientes y recientes
        if (applicationsResponse.data) {
          this.stats.pendingApplications = applicationsResponse.data.filter(
            app => app.estado === 'pendiente'
          ).length
          
          const weekAgo = new Date()
          weekAgo.setDate(weekAgo.getDate() - 7)
          this.stats.recentApplications = applicationsResponse.data.filter(
            app => new Date(app.created_at) > weekAgo
          ).length
        }
      } catch (error) {
        console.error('Error loading stats:', error)
      }
    },
    async loadRecentActivity() {
      try {
        const [jobsResponse, applicationsResponse] = await Promise.all([
          jobService.getRecentJobs(),
          applicationService.getMyApplications({ per_page: 5 })
        ])
        
        const activities = []
        
        // Agregar trabajos recientes como actividad
        if (jobsResponse.data) {
          jobsResponse.data.slice(0, 3).forEach(job => {
            activities.push({
              id: `job-${job.id}`,
              type: 'job',
              title: `Nuevo trabajo: ${job.titulo}`,
              description: `Sueldo: $${this.formatSalary(job.sueldo)}`,
              created_at: job.created_at,
              data: job
            })
          })
        }
        
        // Agregar postulaciones recientes como actividad
        if (applicationsResponse.data) {
          applicationsResponse.data.slice(0, 3).forEach(app => {
            activities.push({
              id: `app-${app.id}`,
              type: 'application',
              title: `Postulación: ${app.trabajo.titulo}`,
              description: `Estado: ${this.getStatusText(app.estado)}`,
              created_at: app.created_at,
              data: app
            })
          })
        }
        
        // Ordenar por fecha y tomar los 5 más recientes
        this.recentActivity = activities
          .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
          .slice(0, 5)
      } catch (error) {
        console.error('Error loading recent activity:', error)
      }
    },
    goToJobs() {
      this.$router.push('/jobs')
    },
    goToApplications() {
      this.$router.push('/applications')
    },
    handleActivityAction(activity) {
      if (activity.type === 'job') {
        this.$router.push('/jobs')
      } else if (activity.type === 'application') {
        this.$router.push('/applications')
      }
    },
    getActivityIcon(type) {
      const icons = {
        'job': 'fas fa-briefcase',
        'application': 'fas fa-paper-plane'
      }
      return icons[type] || 'fas fa-info-circle'
    },
    getActivityColor(type) {
      const colors = {
        'job': '#007bff',
        'application': '#28a745'
      }
      return colors[type] || '#6c757d'
    },
    formatSalary(salary) {
      return parseFloat(salary).toLocaleString('es-CO')
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('es-CO')
    }
  }
}
</script>

<style scoped>
.dashboard {
  min-height: 100vh;
}

.stats-card {
  transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
  transform: translateY(-2px);
}

.action-card {
  transition: transform 0.2s ease-in-out;
  border: 1px solid #e9ecef;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.activity-item {
  padding: 1rem 0;
}

.activity-icon {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f9fa;
  border-radius: 50%;
}

.activity-icon i {
  font-size: 1.2rem;
}

.activity-content h6 {
  margin-bottom: 0.25rem;
  font-weight: 600;
}

.activity-content p {
  margin-bottom: 0.25rem;
  font-size: 0.9rem;
}

.activity-content small {
  font-size: 0.8rem;
}

.activity-action {
  margin-left: 1rem;
}
</style> 