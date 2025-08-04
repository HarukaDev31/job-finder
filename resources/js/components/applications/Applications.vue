<template>
  <div class="applications">
    <!-- Header -->
    <b-row class="mb-4">
      <b-col>
        <b-card bg-variant="light" border-variant="success">
          <b-card-body class="text-center">
            <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
            <h3>Mis Postulaciones</h3>
            <p class="lead">Revisa el estado de todas tus postulaciones</p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Stats Cards -->
    <b-row class="mb-4">
      <b-col md="3">
        <b-card bg-variant="primary" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-paper-plane fa-2x mb-2"></i>
            <h4>{{ applicationsPagination ? applicationsPagination.total : 0 }}</h4>
            <p class="mb-0">Total Postulaciones</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="warning" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-clock fa-2x mb-2"></i>
            <h4>{{ pendingCount }}</h4>
            <p class="mb-0">Pendientes</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="success" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-check-circle fa-2x mb-2"></i>
            <h4>{{ acceptedCount }}</h4>
            <p class="mb-0">Aceptadas</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="danger" text-variant="white" class="text-center stats-card">
          <b-card-body>
            <i class="fas fa-times-circle fa-2x mb-2"></i>
            <h4>{{ rejectedCount }}</h4>
            <p class="mb-0">Rechazadas</p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Applications Management -->
    <b-card>
      <b-card-header>
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="fas fa-list mr-2"></i>
            Historial de Postulaciones
          </h5>
          <div>
            <b-form-select
              v-model="statusFilter"
              :options="statusOptions"
              size="sm"
              style="width: auto; margin-right: 10px;"
              @change="loadApplications(1)"
            ></b-form-select>
            <b-button variant="info" @click="loadApplications(1)" :disabled="loading">
              <i class="fas fa-sync-alt mr-1"></i>
              Actualizar
            </b-button>
          </div>
        </div>
      </b-card-header>
      <b-card-body>
        <!-- Search -->
        <b-row class="mb-3">
          <b-col md="6">
            <b-input-group>
              <b-form-input
                v-model="searchTerm"
                placeholder="Buscar por título del trabajo..."
                @input="debounceSearch"
              ></b-form-input>
              <b-input-group-append>
                <b-button variant="outline-secondary">
                  <i class="fas fa-search"></i>
                </b-button>
              </b-input-group-append>
            </b-input-group>
          </b-col>
          <b-col md="6" class="text-right">
            <b-button variant="outline-secondary" @click="clearFilters">
              <i class="fas fa-times mr-1"></i>
              Limpiar Filtros
            </b-button>
          </b-col>
        </b-row>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-4">
          <b-spinner variant="info" label="Cargando postulaciones..."></b-spinner>
          <p class="mt-3">Cargando tus postulaciones...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-4">
          <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
          <p class="text-danger">{{ error }}</p>
          <b-button variant="outline-info" @click="loadApplications(1)">
            <i class="fas fa-redo mr-1"></i>
            Reintentar
          </b-button>
        </div>

        <!-- Empty State -->
        <div v-else-if="applications.length === 0" class="text-center py-4">
          <i class="fas fa-paper-plane fa-2x text-muted mb-3"></i>
          <p class="text-muted">Aún no te has postulado a ningún trabajo.</p>
          <b-button variant="primary" @click="goToJobs">
            <i class="fas fa-search mr-1"></i>
            Buscar Trabajos
          </b-button>
        </div>

        <!-- Applications List -->
        <div v-else>
          <b-row>
            <b-col 
              v-for="application in applications" 
              :key="application.id" 
              md="6" 
              lg="4" 
              class="mb-3">
              <b-card class="h-100 application-card">
                <b-card-body>
                  <h6 class="card-title">{{ application.trabajo.titulo }}</h6>
                  <p class="card-text text-muted small">
                    {{ application.mensaje.length > 80 ? application.mensaje.substring(0, 80) + '...' : application.mensaje }}
                  </p>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <b-badge :variant="getStatusVariant(application.estado)">
                      {{ getStatusText(application.estado) }}
                    </b-badge>
                    <small class="text-muted">
                      {{ formatDate(application.created_at) }}
                    </small>
                  </div>
                  <div class="text-success font-weight-bold">
                    ${{ formatSalary(application.trabajo.sueldo) }}
                  </div>
                </b-card-body>
                <b-card-footer class="bg-transparent">
                  <b-button 
                    variant="outline-info" 
                    size="sm" 
                    block
                    @click="viewApplication(application)"
                  >
                    <i class="fas fa-eye mr-1"></i>
                    Ver Detalles
                  </b-button>
                </b-card-footer>
              </b-card>
            </b-col>
          </b-row>

          <!-- Pagination -->
          <Pagination 
            v-if="applicationsPagination"
            :pagination="applicationsPagination"
            @page-changed="onApplicationsPageChanged"
            @per-page-changed="onApplicationsPerPageChanged"
          />
        </div>
      </b-card-body>
    </b-card>

    <!-- Application Details Modal -->
    <b-modal v-model="showApplicationDetailsModal" title="Detalles de la Postulación" size="lg" scrollable>
      <div v-if="selectedApplication">
        <div class="row">
          <div class="col-md-6">
            <h6>Información del Trabajo</h6>
            <p><strong>Título:</strong> {{ selectedApplication.trabajo.titulo }}</p>
            <p><strong>Sueldo:</strong> ${{ formatSalary(selectedApplication.trabajo.sueldo) }}</p>
            <p><strong>Estado:</strong> 
              <b-badge :variant="getStatusVariant(selectedApplication.estado)">
                {{ getStatusText(selectedApplication.estado) }}
              </b-badge>
            </p>
          </div>
          <div class="col-md-6">
            <h6>Información de la Postulación</h6>
            <p><strong>Fecha de postulación:</strong> {{ formatDate(selectedApplication.created_at) }}</p>
            <p><strong>CV enviado:</strong> 
              <a href="#" @click.prevent="downloadCV(selectedApplication)">
                <i class="fas fa-download mr-1"></i>
                Descargar CV
              </a>
            </p>
          </div>
        </div>

        <hr>

        <h6>Descripción del Trabajo</h6>
        <div class="p-3 bg-light rounded mb-3">
          <p class="mb-0">{{ selectedApplication.trabajo.descripcion }}</p>
        </div>

        <h6>Tu Mensaje de Motivación</h6>
        <div class="p-3 bg-light rounded">
          <p class="mb-0">{{ selectedApplication.mensaje }}</p>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import Pagination from '@/components/common/Pagination.vue'
import applicationStatusMixin from '@/mixins/applicationStatusMixin.js'
import applicationService from '@/services/applicationService'

export default {
  name: 'Applications',
  components: {
    Pagination
  },
  mixins: [applicationStatusMixin],
  data() {
    return {
      loading: false,
      error: '',
      applications: [],
      applicationsPagination: null,
      searchTerm: '',
      searchTimeout: null,
      statusFilter: '',
      showApplicationDetailsModal: false,
      selectedApplication: null
    }
  },
  computed: {
    pendingCount() {
      return this.applications.filter(app => app.estado === 'pendiente').length
    },
    acceptedCount() {
      return this.applications.filter(app => app.estado === 'aceptado').length
    },
    rejectedCount() {
      return this.applications.filter(app => app.estado === 'rechazado').length
    }
  },
  mounted() {
    this.loadApplications(1)
  },
  methods: {
    async loadApplications(page = 1) {
      this.loading = true
      this.error = ''
      
      try {
        const params = {
          page: page,
          per_page: this.applicationsPagination?.per_page || 15
        }

        if (this.searchTerm) {
          params.search = this.searchTerm
        }

        if (this.statusFilter) {
          params.status = this.statusFilter
        }

        const response = await applicationService.getMyApplications(params)
        
        if (response.success) {
          this.applications = response.data || []
          this.applicationsPagination = response.pagination || null
        }
      } catch (error) {
        console.error('Error loading applications:', error)
        this.error = 'Error al cargar las postulaciones. Por favor, intente nuevamente.'
      } finally {
        this.loading = false
      }
    },
    debounceSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      
      this.searchTimeout = setTimeout(() => {
        this.loadApplications(1)
      }, 500)
    },
    clearFilters() {
      this.searchTerm = ''
      this.statusFilter = ''
      this.loadApplications(1)
    },
    viewApplication(application) {
      this.selectedApplication = application
      this.showApplicationDetailsModal = true
    },
    async downloadCV(application) {
      try {
        this.$bvToast.toast('Descargando CV...', {
          title: 'Info',
          variant: 'info',
          solid: true
        })
        
        // Usar el servicio para descargar el archivo
        const response = await applicationService.downloadCV(application.id)
        
        // Crear URL del blob
        const blob = new Blob([response.data])
        const url = window.URL.createObjectURL(blob)
        
        // Crear enlace temporal para descarga
        const link = document.createElement('a')
        link.href = url
        
        // Generar nombre de archivo descriptivo
        const applicantName = application.postulante.user.name.replace(/[^a-zA-Z0-9]/g, '_')
        const jobTitle = application.trabajo.titulo.replace(/[^a-zA-Z0-9]/g, '_')
        link.download = `CV_${applicantName}_${jobTitle}.pdf`
        
        // Simular clic y limpiar
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
        
        this.$bvToast.toast('CV descargado correctamente', {
          title: 'Éxito',
          variant: 'success',
          solid: true
        })
      } catch (error) {
        console.error('Error downloading CV:', error)
        this.$bvToast.toast('Error al descargar el CV', {
          title: 'Error',
          variant: 'danger',
          solid: true
        })
      }
    },
    goToJobs() {
      this.$router.push('/jobs')
    },
    formatSalary(salary) {
      return parseFloat(salary).toLocaleString('es-CO')
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('es-CO')
    },
    onApplicationsPageChanged(page) {
      this.loadApplications(page)
    },
    onApplicationsPerPageChanged(perPage) {
      this.loadApplications(1)
    }
  }
}
</script>

<style scoped>
.applications {
  min-height: 100vh;
}

.stats-card {
  transition: transform 0.2s ease-in-out;
}

.stats-card:hover {
  transform: translateY(-2px);
}

.application-card {
  transition: transform 0.2s ease-in-out;
  border: 1px solid #e9ecef;
}

.application-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-footer {
  border-top: 1px solid #e9ecef;
  background-color: transparent;
}
</style> 