<template>
  <div class="home">
    <!-- Hero Section -->
    <b-jumbotron class="text-center bg-primary text-white mb-5">
      <template #header>
        <i class="fas fa-briefcase fa-3x mb-3"></i>
        <h1>Job Finder</h1>
      </template>
      
      <template #lead>
        <p class="lead">Encuentra tu trabajo ideal o encuentra al candidato perfecto</p>
        <p>Portal de empleo moderno y eficiente</p>
      </template>
      
      <hr class="my-4">
      
      <div class="d-flex justify-content-center">
        <b-button 
          v-if="!isAuthenticated" 
          variant="light" 
          size="lg" 
          to="/register"
          class="mr-3">
          <i class="fas fa-user-plus mr-2"></i>
          Registrarse
        </b-button>
        <b-button 
          v-if="!isAuthenticated" 
          variant="outline-light" 
          size="lg" 
          to="/login">
          <i class="fas fa-sign-in-alt mr-2"></i>
          Iniciar Sesión
        </b-button>
        <b-button 
          v-if="isAuthenticated" 
          variant="light" 
          size="lg" 
          to="/dashboard">
          <i class="fas fa-tachometer-alt mr-2"></i>
          Ir al Dashboard
        </b-button>
      </div>
    </b-jumbotron>

    <!-- Features Section -->
    <b-row class="mb-5">
      <b-col md="4" class="mb-4">
        <b-card class="h-100 text-center">
          <b-card-body>
            <i class="fas fa-search fa-3x text-primary mb-3"></i>
            <h4>Buscar Trabajos</h4>
            <p class="card-text">
              Explora cientos de ofertas de trabajo de diferentes empresas y sectores.
              Filtra por ubicación, salario y tipo de contrato.
            </p>
          </b-card-body>
        </b-card>
      </b-col>
      
      <b-col md="4" class="mb-4">
        <b-card class="h-100 text-center">
          <b-card-body>
            <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
            <h4>Postular Fácilmente</h4>
            <p class="card-text">
              Postúlate a los trabajos que te interesen con solo unos clics.
              Sube tu CV y envía tu mensaje de presentación.
            </p>
          </b-card-body>
        </b-card>
      </b-col>
      
      <b-col md="4" class="mb-4">
        <b-card class="h-100 text-center">
          <b-card-body>
            <i class="fas fa-users fa-3x text-info mb-3"></i>
            <h4>Gestionar Candidatos</h4>
            <p class="card-text">
              Para empresas: gestiona las postulaciones, revisa CVs y contacta
              con los mejores candidatos de forma eficiente.
            </p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Stats Section -->
    <b-row class="mb-5">
      <b-col>
        <b-card bg-variant="light" class="text-center">
          <b-card-body>
            <h3 class="mb-4">Estadísticas del Portal</h3>
            <b-row>
              <b-col md="3" class="mb-3">
                <div class="text-center">
                  <i class="fas fa-briefcase fa-2x text-primary mb-2"></i>
                  <h4 class="text-primary">{{ stats.jobs || 0 }}</h4>
                  <p class="mb-0">Trabajos Activos</p>
                </div>
              </b-col>
              <b-col md="3" class="mb-3">
                <div class="text-center">
                  <i class="fas fa-users fa-2x text-success mb-2"></i>
                  <h4 class="text-success">{{ stats.users || 0 }}</h4>
                  <p class="mb-0">Usuarios Registrados</p>
                </div>
              </b-col>
              <b-col md="3" class="mb-3">
                <div class="text-center">
                  <i class="fas fa-paper-plane fa-2x text-info mb-2"></i>
                  <h4 class="text-info">{{ stats.applications || 0 }}</h4>
                  <p class="mb-0">Postulaciones</p>
                </div>
              </b-col>
              <b-col md="3" class="mb-3">
                <div class="text-center">
                  <i class="fas fa-building fa-2x text-warning mb-2"></i>
                  <h4 class="text-warning">{{ stats.companies || 0 }}</h4>
                  <p class="mb-0">Empresas</p>
                </div>
              </b-col>
            </b-row>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Recent Jobs Section -->
    <b-row class="mb-5">
      <b-col>
        <b-card>
          <b-card-header>
            <h4 class="mb-0">
              <i class="fas fa-clock mr-2"></i>
              Trabajos Recientes
            </h4>
          </b-card-header>
          <b-card-body>
            <div v-if="loading" class="text-center">
              <b-spinner></b-spinner>
              <p class="mt-2">Cargando trabajos...</p>
            </div>
            
            <div v-else-if="recentJobs.length === 0" class="text-center">
              <i class="fas fa-search fa-2x text-muted mb-3"></i>
              <p class="text-muted">No hay trabajos disponibles en este momento.</p>
            </div>
            
            <b-row v-else>
              <b-col 
                v-for="job in recentJobs" 
                :key="job.id" 
                md="6" 
                lg="4" 
                class="mb-3">
                <b-card class="h-100 job-card">
                  <b-card-body>
                    <h5 class="card-title">{{ job.titulo }}</h5>
                    <p class="card-text text-muted">
                      {{ job.descripcion.length > 100 ? job.descripcion.substring(0, 100) + '...' : job.descripcion }}
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="text-success font-weight-bold">
                        ${{ formatSalary(job.sueldo) }}
                      </span>
                      <small class="text-muted">
                        {{ formatDate(job.created_at) }}
                      </small>
                    </div>
                  </b-card-body>
                  <b-card-footer>
                    <b-button 
                      variant="primary" 
                      size="sm" 
                      block
                      @click="viewJob(job)">
                      Ver Detalles
                    </b-button>
                  </b-card-footer>
                </b-card>
              </b-col>
            </b-row>
            
            <div class="text-center mt-3">
              <b-button variant="outline-primary" to="/jobs">
                Ver Todos los Trabajos
              </b-button>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Footer -->
    <b-row>
      <b-col>
        <b-card bg-variant="dark" text-variant="white" class="text-center">
          <b-card-body>
            <p class="mb-0">
              © 2024 Job Finder. Todos los derechos reservados.
            </p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import jobService from '@/services/jobService'
import dashboardService from '@/services/dashboardService'

export default {
  name: 'Home',
  data() {
    return {
      loading: false,
      recentJobs: [],
      stats: {
        jobs: 0,
        users: 0,
        applications: 0,
        companies: 0
      }
    }
  },
  computed: {
    isAuthenticated() {
      return this.$root.isAuthenticated
    }
  },
  mounted() {
    this.loadRecentJobs()
    this.loadStats()
  },
  methods: {
    async loadRecentJobs() {
      this.loading = true
      try {
        const response = await jobService.getRecentJobs()
        this.recentJobs = response.data || []
      } catch (error) {
        console.error('Error loading recent jobs:', error)
      } finally {
        this.loading = false
      }
    },
    async loadStats() {
      try {
        const response = await dashboardService.getStats()
        this.stats = response.data || {}
      } catch (error) {
        console.error('Error loading stats:', error)
      }
    },
    formatSalary(salary) {
      return parseFloat(salary).toLocaleString('es-CO')
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('es-CO')
    },
    viewJob(job) {
      if (this.isAuthenticated) {
        this.$router.push(`/jobs/${job.id}`)
      } else {
        this.$router.push('/login')
      }
    }
  }
}
</script>

<style scoped>
.job-card {
  transition: transform 0.2s ease-in-out;
}

.job-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.home {
  min-height: 100vh;
}
</style> 