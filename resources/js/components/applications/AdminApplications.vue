<template>
  <div class="admin-applications">
    <!-- Header -->
    <b-row class="mb-4">
      <b-col>
        <b-card bg-variant="light" border-variant="primary">
          <b-card-body class="text-center">
            <i class="fas fa-users fa-3x text-primary mb-3"></i>
            <h3>Gestión de Postulaciones</h3>
            <p class="lead">Administra y revisa todas las postulaciones del portal</p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Stats Cards -->
    <b-row class="mb-4">
      <b-col md="3">
        <b-card bg-variant="primary" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-paper-plane fa-2x mb-2"></i>
            <h4>{{ metrics.totalApplications || 0 }}</h4>
            <p class="mb-0">Total Postulaciones</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="warning" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-clock fa-2x mb-2"></i>
            <h4>{{ metrics.pendingApplications || 0 }}</h4>
            <p class="mb-0">Pendientes</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="success" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-check-circle fa-2x mb-2"></i>
            <h4>{{ metrics.acceptedApplications || 0 }}</h4>
            <p class="mb-0">Aceptadas</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="danger" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-times-circle fa-2x mb-2"></i>
            <h4>{{ metrics.rejectedApplications || 0 }}</h4>
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
            Todas las Postulaciones
            <b-badge variant="info" class="ml-2">{{ applicationsPagination ? applicationsPagination.total : 0 }}</b-badge>
          </h5>
          <div>
            <b-form-select
              v-model="statusFilter"
              :options="statusOptions"
              size="sm"
              style="width: auto; margin-right: 10px;"
              @change="loadApplications(1)"
            ></b-form-select>
            <b-button variant="outline-secondary" @click="loadApplications(1)" class="mr-2">
              <i class="fas fa-sync-alt mr-1"></i>
              Actualizar
            </b-button>
            <b-button variant="info" @click="showFilters = !showFilters">
              <i class="fas fa-filter mr-1"></i>
              Filtros
            </b-button>
          </div>
        </div>
      </b-card-header>
      <b-card-body>
        <!-- Search and Filters -->
        <b-row class="mb-3">
          <b-col md="6">
            <b-input-group>
              <b-form-input
                v-model="searchTerm"
                placeholder="Buscar por nombre, email o título del trabajo..."
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

        <!-- Advanced Filters -->
        <div v-if="showFilters" class="mb-4 p-3 bg-light rounded">
          <b-row>
            <b-col md="3">
              <b-form-group label="Trabajo:" label-for="jobFilter">
                <b-form-select id="jobFilter" v-model="filters.job" :options="jobOptions" />
              </b-form-group>
            </b-col>
            <b-col md="3">
              <b-form-group label="Fecha desde:" label-for="dateFrom">
                <b-form-input id="dateFrom" v-model="filters.dateFrom" type="date" />
              </b-form-group>
            </b-col>
            <b-col md="3">
              <b-form-group label="Fecha hasta:" label-for="dateTo">
                <b-form-input id="dateTo" v-model="filters.dateTo" type="date" />
              </b-form-group>
            </b-col>
            <b-col md="3">
              <b-form-group label="Ordenar por:" label-for="sortBy">
                <b-form-select id="sortBy" v-model="filters.sortBy" :options="sortOptions" />
              </b-form-group>
            </b-col>
          </b-row>
          <div class="text-right">
            <b-button variant="secondary" @click="clearFilters" class="mr-2">
              Limpiar
            </b-button>
            <b-button variant="primary" @click="applyFilters">
              Aplicar
            </b-button>
          </div>
        </div>

        <div v-if="loading" class="text-center">
          <b-spinner></b-spinner>
          <p class="mt-2">Cargando postulaciones...</p>
        </div>

        <div v-else-if="applications.length === 0" class="text-center">
          <i class="fas fa-paper-plane fa-2x text-muted mb-3"></i>
          <p class="text-muted">No hay postulaciones registradas.</p>
        </div>

        <b-table v-else :items="applications" :fields="applicationFields" striped hover responsive show-empty
          empty-text="No hay postulaciones disponibles">

          <template #cell(postulante.user.name)="data">
            <strong>{{ data.item.postulante.user.name }}</strong>
            <br>
            <small class="text-muted">{{ data.item.postulante.user.email }}</small>
          </template>

          <template #cell(trabajo.titulo)="data">
            <span>{{ data.item.trabajo.titulo }}</span>
            <br>
            <small class="text-muted">${{ formatSalary(data.item.trabajo.sueldo) }}</small>
          </template>

          <template #cell(mensaje)="data">
            <span>{{ data.item.mensaje.length > 50 ? data.item.mensaje.substring(0, 50) + '...' : data.item.mensaje
            }}</span>
          </template>

          <template #cell(estado)="data">
            <b-badge :variant="getStatusVariant(data.item.estado)">
              {{ getStatusText(data.item.estado) }}
            </b-badge>
          </template>

          <template #cell(created_at)="data">
            {{ formatDate(data.item.created_at) }}
          </template>

          <template #cell(actions)="data">
            <b-button-group size="sm">
              <b-button variant="info" @click="viewApplication(data.item)" title="Ver detalles">
                <i class="fas fa-eye"></i>
              </b-button>
              <b-button variant="warning" @click="downloadCV(data.item)" title="Descargar CV">
                <i class="fas fa-download"></i>
              </b-button>
              <b-button variant="success" @click="updateApplicationStatus(data.item, 'aceptado')"
                :disabled="data.item.estado === 'aceptado'" title="Aceptar">
                <i class="fas fa-check"></i>
              </b-button>
              <b-button variant="danger" @click="updateApplicationStatus(data.item, 'rechazada')"
                :disabled="data.item.estado === 'rechazado'" title="Rechazar">
                <i class="fas fa-times"></i>
              </b-button>
            </b-button-group>
          </template>
        </b-table>

        <!-- Pagination -->
        <Pagination 
          v-if="applicationsPagination"
          :pagination="applicationsPagination"
          @page-changed="onApplicationsPageChanged"
          @per-page-changed="onApplicationsPerPageChanged"
        />
      </b-card-body>
    </b-card>

    <!-- Application Details Modal -->
    <b-modal v-model="showApplicationDetailsModal" title="Detalles de la Postulación" size="lg" scrollable>
      <div v-if="selectedApplication">
        <div class="row">
          <div class="col-md-6">
            <h6>Información del Postulante</h6>
            <p><strong>Nombre:</strong> {{ selectedApplication.postulante.user.name }}</p>
            <p><strong>Email:</strong> {{ selectedApplication.postulante.user.email }}</p>
            <p><strong>Fecha de registro:</strong> {{ formatDate(selectedApplication.postulante.user.created_at) }}</p>
          </div>
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
        </div>

        <hr>

        <h6>Mensaje de Postulación</h6>
        <div class="p-3 bg-light rounded">
          <p class="mb-0">{{ selectedApplication.mensaje }}</p>
        </div>

        <hr>

        <h6>Acciones</h6>
        <div class="d-flex justify-content-center">
          <b-button variant="warning" @click="downloadCV(selectedApplication)" class="mr-2">
            <i class="fas fa-download mr-1"></i>
            Descargar CV
          </b-button>
          <b-button variant="success" @click="updateApplicationStatus(selectedApplication, 'aceptado')"
            :disabled="selectedApplication.estado === 'aceptado'" class="mr-2">
            <i class="fas fa-check mr-1"></i>
            Aceptar Postulación
          </b-button>
          <b-button variant="danger" @click="updateApplicationStatus(selectedApplication, 'rechazado')"
            :disabled="selectedApplication.estado === 'rechazado'" class="mr-2">
            <i class="fas fa-times mr-1"></i>
            Rechazar Postulación
          </b-button>
          <b-button variant="info" @click="updateApplicationStatus(selectedApplication, 'revisado')"
            :disabled="selectedApplication.estado === 'revisado'">
            <i class="fas fa-eye mr-1"></i>
            Marcar en Revisión
          </b-button>
        </div>
      </div>
    </b-modal>

    <!-- Alert Modal -->
    <b-modal v-model="showAlert" :title="alertTitle" :header-bg-variant="alertType"
      :header-text-variant="alertType === 'danger' || alertType === 'success' ? 'white' : 'dark'" centered
      @ok="closeAlert" @cancel="closeAlert" @close="closeAlert">

      <div class="text-center">
        <i :class="[alertIcon, 'fa-3x mb-3', {
          'text-success': alertType === 'success',
          'text-danger': alertType === 'danger',
          'text-warning': alertType === 'warning',
          'text-info': alertType === 'info'
        }]"></i>
        <div v-html="alertMessage"></div>
      </div>

      <template #modal-footer="{ ok, cancel }">
        <b-button :variant="alertType" @click="ok" block>
          Entendido
        </b-button>
      </template>
    </b-modal>
  </div>
</template>

<script>
import alertMixin from '@/mixins/alertMixin.js'
import applicationStatusMixin from '@/mixins/applicationStatusMixin.js'
import statusOptionsMixin from '@/mixins/statusOptionsMixin.js'
import Pagination from '@/components/common/Pagination.vue'
import applicationService from '@/services/applicationService'
import dashboardService from '@/services/dashboardService'
import jobService from '@/services/jobService'

export default {
  name: 'AdminApplications',
  mixins: [alertMixin, applicationStatusMixin, statusOptionsMixin],
  components: {
    Pagination
  },
  data() {
    return {
      loading: false,
      applications: [],
      applicationsPagination: null,
      metrics: {},
      showApplicationDetailsModal: false,
      selectedApplication: null,
      showFilters: false,
      searchTerm: '',
      searchTimeout: null,
      statusFilter: '',
      filters: {
        job: '',
        dateFrom: '',
        dateTo: '',
        sortBy: 'created_at'
      },
      applicationFields: [
        { key: 'postulante.user.name', label: 'Postulante', sortable: true },
        { key: 'trabajo.titulo', label: 'Trabajo', sortable: true },
        { key: 'mensaje', label: 'Mensaje', sortable: false },
        { key: 'estado', label: 'Estado', sortable: true },
        { key: 'created_at', label: 'Fecha', sortable: true },
        { key: 'actions', label: 'Acciones', sortable: false }
      ],
      jobOptions: [
        { value: '', text: 'Todos los trabajos' }
      ],
      sortOptions: [
        { value: 'created_at', text: 'Fecha de postulación' },
        { value: 'estado', text: 'Estado' },
        { value: 'postulante.user.name', text: 'Nombre del postulante' },
        { value: 'trabajo.titulo', text: 'Título del trabajo' }
      ]
    }
  },
  mounted() {
    this.loadData()
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        await Promise.all([
          this.loadApplications(1),
          this.loadMetrics(),
          this.loadJobs()
        ])
      } catch (error) {
        console.error('Error loading data:', error)
        this.showErrorAlert('Error', 'Error al cargar los datos')
      } finally {
        this.loading = false
      }
    },
    async loadApplications(page = 1) {
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

        // Agregar filtros avanzados si están configurados
        if (this.filters.job) {
          params.job_id = this.filters.job
        }

        if (this.filters.dateFrom) {
          params.date_from = this.filters.dateFrom
        }

        if (this.filters.dateTo) {
          params.date_to = this.filters.dateTo
        }

        if (this.filters.sortBy) {
          params.sort_by = this.filters.sortBy
        }

        const response = await applicationService.getAllApplications(params)
        
        if (response.success) {
          this.applications = response.data || []
          this.applicationsPagination = response.pagination || null
        }
      } catch (error) {
        this.showErrorAlert('Error', 'Error al cargar las postulaciones')
      }
    },
    async loadMetrics() {
      try {
        const response = await dashboardService.getAdminMetrics()
        this.metrics = response.data || {}
      } catch (error) {
        console.error('Error loading metrics:', error)
        this.showErrorAlert('Error', 'Error al cargar las métricas')
      }
    },
    async loadJobs() {
      try {
        const response = await jobService.getAdminJobs()
        const jobs = response.data || []
        this.jobOptions = [
          { value: '', text: 'Todos los trabajos' },
          ...jobs.map(job => ({
            value: job.id,
            text: job.titulo
          }))
        ]
      } catch (error) {
        console.error('Error loading jobs:', error)
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
      this.filters = {
        job: '',
        dateFrom: '',
        dateTo: '',
        sortBy: 'created_at'
      }
      this.loadApplications(1)
    },
    applyFilters() {
      this.loadApplications(1)
    },
    viewApplication(application) {
      this.selectedApplication = application
      this.showApplicationDetailsModal = true
    },
    async updateApplicationStatus(application, status) {
      try {
        const response = await applicationService.updateApplicationStatus(application.id, { 
          estado: status 
        })
        
        this.showSuccessAlert('Éxito', 'Estado actualizado correctamente')
        this.loadApplications(1) // Recargar la lista
      } catch (error) {
        console.error('Error updating application status:', error)
        this.showErrorAlert('Error', 'Error al actualizar el estado')
      }
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
      // Actualizar el per_page en la paginación
      if (this.applicationsPagination) {
        this.applicationsPagination.per_page = perPage
      }
      this.loadApplications(1)
    },
    async downloadCV(application) {
      try {
        this.$bvToast.toast('Descargando CV...', {
          title: 'Info',
          variant: 'info',
          solid: true
        })
        
        // Usar axios para descargar el archivo
        const response = await axios.get(`/api/applications/${application.id}/cv`, {
          responseType: 'blob'
        })
        
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
    }
  }
}
</script>

<style scoped>
.admin-applications {
  min-height: 100vh;
  padding: 20px;
}
</style> 