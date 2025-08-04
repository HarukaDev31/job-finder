<template>
  <div class="admin-jobs">
    <!-- Header -->
    <b-row class="mb-4">
      <b-col>
        <b-card bg-variant="light" border-variant="primary">
          <b-card-body class="text-center">
            <i class="fas fa-briefcase fa-3x text-primary mb-3"></i>
            <h3>Gestión de Trabajos</h3>
            <p class="lead">Administra y gestiona todos los trabajos del portal</p>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Stats Cards -->
    <b-row class="mb-4">
      <b-col md="3">
        <b-card bg-variant="primary" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-briefcase fa-2x mb-2"></i>
            <h4>{{ metrics.totalJobs || 0 }}</h4>
            <p class="mb-0">Total Trabajos</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="success" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-check-circle fa-2x mb-2"></i>
            <h4>{{ metrics.activeJobs || 0 }}</h4>
            <p class="mb-0">Trabajos Activos</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="info" text-variant="white" class="text-center">
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
    </b-row>

    <!-- Jobs Management -->
    <b-card>
      <b-card-header>
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="fas fa-list mr-2"></i>
            Trabajos Publicados
            <b-badge variant="info" class="ml-2">{{ jobsPagination ? jobsPagination.total : 0 }}</b-badge>
          </h5>
          <div>
            <b-form-select
              v-model="jobStatusFilter"
              :options="statusOptions"
              size="sm"
              style="width: auto; margin-right: 10px;"
              @change="loadJobs(1)"
            ></b-form-select>
            <b-button variant="success" @click="showCreateJobModal = true">
              <i class="fas fa-plus mr-1"></i>
              Nuevo Trabajo
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
                placeholder="Buscar trabajos..."
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

        <div v-if="loading" class="text-center">
          <b-spinner></b-spinner>
          <p class="mt-2">Cargando trabajos...</p>
        </div>

        <div v-else-if="jobs.length === 0" class="text-center">
          <i class="fas fa-briefcase fa-2x text-muted mb-3"></i>
          <p class="text-muted">No hay trabajos publicados.</p>
          <b-button variant="success" @click="showCreateJobModal = true">
            <i class="fas fa-plus mr-1"></i>
            Crear Primer Trabajo
          </b-button>
        </div>

        <b-table v-else :items="jobs" :fields="jobFields" striped hover responsive show-empty
          empty-text="No hay trabajos disponibles">

          <template #cell(titulo)="data">
            <strong>{{ data.item.titulo }}</strong>
          </template>

          <template #cell(descripcion)="data">
            <span>{{ data.item.descripcion.length > 100 ? data.item.descripcion.substring(0, 100) + '...' :
              data.item.descripcion }}</span>
          </template>

          <template #cell(sueldo)="data">
            <span class="text-success font-weight-bold">
              ${{ formatSalary(data.item.sueldo) }}
            </span>
          </template>

          <template #cell(postulaciones_count)="data">
            <b-badge variant="info">{{ data.item.postulaciones_count }}</b-badge>
          </template>

          <template #cell(activo)="data">
            <b-badge :variant="data.item.activo ? 'success' : 'secondary'">
              {{ data.item.activo ? 'Activo' : 'Inactivo' }}
            </b-badge>
          </template>

          <template #cell(created_at)="data">
            {{ formatDate(data.item.created_at) }}
          </template>

          <template #cell(actions)="data">
            <b-button-group size="sm">
              <b-button variant="info" @click="viewJobDetails(data.item)" title="Ver detalles">
                <i class="fas fa-eye"></i>
              </b-button>
              <b-button variant="warning" @click="editJob(data.item)" title="Editar">
                <i class="fas fa-edit"></i>
              </b-button>
              <b-button variant="danger" @click="deleteJob(data.item)" title="Eliminar">
                <i class="fas fa-trash"></i>
              </b-button>
            </b-button-group>
          </template>
        </b-table>

        <!-- Pagination -->
        <Pagination 
          v-if="jobsPagination"
          :pagination="jobsPagination"
          @page-changed="onJobsPageChanged"
          @per-page-changed="onJobsPerPageChanged"
        />
      </b-card-body>
    </b-card>

    <!-- Create Job Modal -->
    <b-modal v-model="showCreateJobModal" title="Crear Nuevo Trabajo" size="lg" @ok="handleCreateJob"
      @hidden="resetJobForm" :ok-disabled="loading || !isJobFormValid">

      <b-form @submit.prevent="handleCreateJob">
        <b-form-group label="Título del Trabajo:" label-for="titulo">
          <b-form-input id="titulo" v-model="jobForm.titulo" :state="validateState('titulo')" required
            placeholder="Ingresa el título del trabajo">
          </b-form-input>
          <b-form-invalid-feedback :state="validateState('titulo')">
            {{ errors.titulo ? errors.titulo[0] : 'El título es obligatorio.' }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Descripción:" label-for="descripcion">
          <b-form-textarea id="descripcion" v-model="jobForm.descripcion" :state="validateState('descripcion')" rows="4"
            required placeholder="Describe las responsabilidades y requisitos del trabajo...">
          </b-form-textarea>
          <b-form-invalid-feedback :state="validateState('descripcion')">
            {{ errors.descripcion ? errors.descripcion[0] : 'La descripción es obligatoria.' }}
          </b-form-invalid-feedback>
        </b-form-group>

        <b-form-group label="Sueldo:" label-for="sueldo">
          <b-form-input id="sueldo" v-model="jobForm.sueldo" :state="validateState('sueldo')" type="number" min="0"
            step="0.01" required placeholder="Ingresa el sueldo">
          </b-form-input>
          <b-form-invalid-feedback :state="validateState('sueldo')">
            {{ errors.sueldo ? errors.sueldo[0] : 'El sueldo es obligatorio.' }}
          </b-form-invalid-feedback>
        </b-form-group>
      </b-form>

      <template #modal-footer="{ ok, cancel }">
        <b-button variant="secondary" @click="cancel">
          Cancelar
        </b-button>
        <b-button variant="success" @click="handleCreateJob" :disabled="loading || !isJobFormValid">
          <b-spinner small v-if="loading"></b-spinner>
          <span v-text="loading ? 'Creando...' : 'Crear Trabajo'"></span>
        </b-button>
      </template>
    </b-modal>

    <!-- Job Details Modal -->
    <b-modal v-model="showJobDetailsModal" title="Detalles del Trabajo" size="lg" scrollable>
      <div v-if="selectedJob">
        <h5>{{ selectedJob.titulo }}</h5>
        <p class="text-muted">{{ selectedJob.descripcion }}</p>

        <div class="row mb-3">
          <div class="col-md-6">
            <strong>Sueldo:</strong> ${{ formatSalary(selectedJob.sueldo) }}
          </div>
          <div class="col-md-6">
            <strong>Postulaciones:</strong> {{ jobApplicationsPagination ? jobApplicationsPagination.total : 0 }}
          </div>
        </div>

        <hr>

        <!-- Filtros para postulaciones -->
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <h6>Postulaciones:</h6>
            <div>
              <b-form-select
                v-model="jobApplicationStatusFilter"
                :options="statusOptions"
                size="sm"
                style="width: auto; margin-right: 10px;"
                @change="loadJobApplications(1)"
              ></b-form-select>
              <b-button variant="info" size="sm" @click="loadJobApplications(1)" :disabled="loadingJobApplications">
                <i class="fas fa-sync-alt mr-1"></i>
                Actualizar
              </b-button>
            </div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadingJobApplications" class="text-center py-3">
          <b-spinner small></b-spinner>
          <p class="mt-2">Cargando postulaciones...</p>
        </div>

        <!-- Applications List -->
        <div v-else-if="jobApplications.length > 0">
          <div v-for="application in jobApplications" :key="application.id" class="mb-3 p-3 border rounded">
            <div class="row">
              <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <strong>{{ application.postulante.user.name }}</strong>
                    <br>
                    <small class="text-muted">{{ application.postulante.user.email }}</small>
                    <br>
                    <small class="text-muted">{{ application.mensaje }}</small>
                  </div>
                  <b-badge :variant="getStatusVariant(application.estado)">
                    {{ getStatusText(application.estado) }}
                  </b-badge>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    <i class="fas fa-calendar mr-1"></i>
                    Postulado: {{ formatDate(application.created_at) }}
                  </small>
                </div>
              </div>
              <div class="col-md-4">
                <div class="d-flex flex-column">
                  <b-button-group size="sm" class="mb-2">
                    <b-button 
                      variant="success" 
                      @click="updateApplicationStatus(application, 'aceptado')"
                      :disabled="application.estado === 'aceptado'"
                      title="Aceptar">
                      <i class="fas fa-check"></i>
                    </b-button>
                    <b-button 
                      variant="danger" 
                      @click="updateApplicationStatus(application, 'rechazado')"
                      :disabled="application.estado === 'rechazado'"
                      title="Rechazar">
                      <i class="fas fa-times"></i>
                    </b-button>
                    <b-button 
                      variant="info" 
                      @click="updateApplicationStatus(application, 'revisado')"
                      :disabled="application.estado === 'revisado'"
                      title="Marcar como Revisado">
                      <i class="fas fa-eye"></i>
                    </b-button>
                  </b-button-group>
                  <b-button 
                    variant="outline-secondary" 
                    size="sm"
                    @click="downloadCV(application)"
                    title="Descargar CV">
                    <i class="fas fa-download mr-1"></i>
                    CV
                  </b-button>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination for Job Applications -->
          <Pagination 
            v-if="jobApplicationsPagination"
            :pagination="jobApplicationsPagination"
            @page-changed="onJobApplicationsPageChanged"
            @per-page-changed="onJobApplicationsPerPageChanged"
          />
        </div>
        <div v-else class="text-center text-muted">
          <p>No hay postulaciones para este trabajo.</p>
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
import Pagination from '@/components/common/Pagination.vue'
import jobService from '@/services/jobService'
import dashboardService from '@/services/dashboardService'
import applicationService from '@/services/applicationService'

export default {
  name: 'AdminJobs',
  mixins: [alertMixin, applicationStatusMixin],
  components: {
    Pagination
  },
  data() {
    return {
      loading: false,
      jobs: [],
      jobsPagination: null,
      metrics: {},
      showCreateJobModal: false,
      showJobDetailsModal: false,
      selectedJob: null,
      searchTerm: '',
      searchTimeout: null,
      jobStatusFilter: '',
      jobForm: {
        titulo: '',
        descripcion: '',
        sueldo: ''
      },
      errors: {},
      jobFields: [
        { key: 'titulo', label: 'Título', sortable: true },
        { key: 'descripcion', label: 'Descripción', sortable: false },
        { key: 'sueldo', label: 'Sueldo', sortable: true },
        { key: 'postulaciones_count', label: 'Postulaciones', sortable: true },
        { key: 'activo', label: 'Estado', sortable: true },
        { key: 'created_at', label: 'Fecha', sortable: true },
        { key: 'actions', label: 'Acciones', sortable: false }
      ],
      // Propiedades para postulaciones de trabajo específico
      jobApplications: [],
      jobApplicationsPagination: null,
      loadingJobApplications: false,
      jobApplicationStatusFilter: ''
    }
  },
  computed: {
    isJobFormValid() {
      return (
        this.jobForm.titulo &&
        this.jobForm.titulo.trim().length >= 3 &&
        this.jobForm.descripcion &&
        this.jobForm.descripcion.trim().length >= 10 &&
        this.jobForm.sueldo &&
        !isNaN(parseFloat(this.jobForm.sueldo)) &&
        parseFloat(this.jobForm.sueldo) >= 0
      )
    }
  },
  mounted() {
    this.loadData()
  },
  watch: {
    'jobForm.titulo'() {
      if (this.errors.titulo) delete this.errors.titulo
    },
    'jobForm.descripcion'() {
      if (this.errors.descripcion) delete this.errors.descripcion
    },
    'jobForm.sueldo'() {
      if (this.errors.sueldo) delete this.errors.sueldo
    }
  },
  methods: {
    async loadData() {
      this.loading = true
      try {
        await Promise.all([
          this.loadJobs(1),
          this.loadMetrics()
        ])
      } catch (error) {
        console.error('Error loading data:', error)
        this.showErrorAlert('Error', 'Error al cargar los datos')
      } finally {
        this.loading = false
      }
    },
    async loadJobs(page = 1) {
      try {
        const params = {
          page: page,
          per_page: this.jobsPagination?.per_page || 15
        }

        if (this.searchTerm) {
          params.search = this.searchTerm
        }

        if (this.jobStatusFilter !== '') {
          params.active = this.jobStatusFilter
        }

        const response = await jobService.getAdminJobs(params)
        
        if (response.success) {
          this.jobs = response.data || []
          this.jobsPagination = response.pagination || null
        }
      } catch (error) {
        this.showErrorAlert('Error', 'Error al cargar los trabajos')
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
    debounceSearch() {
      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout)
      }
      
      this.searchTimeout = setTimeout(() => {
        this.loadJobs(1)
      }, 500)
    },
    clearFilters() {
      this.searchTerm = ''
      this.jobStatusFilter = ''
      this.loadJobs(1)
    },
    async handleCreateJob(bvModalEvent) {
      bvModalEvent.preventDefault()
      const validationErrors = this.validateJobForm()
      if (Object.keys(validationErrors).length > 0) {
        this.errors = validationErrors
        return
      }
      await this.createJob()
    },
    validateJobForm() {
      const errors = {}
      if (!this.jobForm.titulo || this.jobForm.titulo.trim() === '') {
        errors.titulo = ['El título del trabajo es obligatorio']
      } else if (this.jobForm.titulo.trim().length < 3) {
        errors.titulo = ['El título debe tener al menos 3 caracteres']
      }
      if (!this.jobForm.descripcion || this.jobForm.descripcion.trim() === '') {
        errors.descripcion = ['La descripción es obligatoria']
      } else if (this.jobForm.descripcion.trim().length < 10) {
        errors.descripcion = ['La descripción debe tener al menos 10 caracteres']
      }
      if (!this.jobForm.sueldo || this.jobForm.sueldo === '') {
        errors.sueldo = ['El sueldo es obligatorio']
      } else {
        const sueldo = parseFloat(this.jobForm.sueldo)
        if (isNaN(sueldo) || sueldo < 0) {
          errors.sueldo = ['El sueldo debe ser un número válido mayor o igual a 0']
        }
      }
      return errors
    },
    async createJob() {
      this.loading = true
      this.errors = {}
      try {
        const response = await jobService.createJob(this.jobForm)
        this.showSuccessAlert('Éxito', 'Trabajo creado exitosamente')
        this.showCreateJobModal = false
        this.loadJobs(1)
      } catch (error) {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors
          this.showValidationError(error.response.data.errors)
        } else {
          this.showErrorAlert('Error', 'Error al crear el trabajo')
        }
      } finally {
        this.loading = false
      }
    },
    viewJobDetails(job) {
      this.selectedJob = job
      this.showJobDetailsModal = true
      this.loadJobApplications(1) // Cargar postulaciones al abrir el modal
    },
    async loadJobApplications(page = 1) {
      if (!this.selectedJob) return
      
      this.loadingJobApplications = true
      try {
        const params = {
          page: page,
          per_page: this.jobApplicationsPagination?.per_page || 15
        }

        if (this.jobApplicationStatusFilter) {
          params.status = this.jobApplicationStatusFilter
        }

        const response = await applicationService.getJobApplications(this.selectedJob.id, params)
        
        if (response.success) {
          this.jobApplications = response.data || []
          this.jobApplicationsPagination = response.pagination || null
        }
      } catch (error) {
        console.error('Error loading job applications:', error)
        this.showErrorAlert('Error', 'Error al cargar las postulaciones del trabajo')
      } finally {
        this.loadingJobApplications = false
      }
    },
    async updateApplicationStatus(application, status) {
      try {
        const response = await applicationService.updateApplicationStatus(application.id, { 
          estado: status 
        })
        
        this.showSuccessAlert('Éxito', 'Estado actualizado correctamente')
        this.loadJobApplications(1) // Recargar la lista de postulaciones
        this.loadJobs() // Recargar la lista de trabajos para actualizar contadores
      } catch (error) {
        console.error('Error updating application status:', error)
        this.showErrorAlert('Error', 'Error al actualizar el estado')
      }
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
    editJob(job) {
      this.showInfoAlert('Información', 'Función de edición en desarrollo')
    },
    deleteJob(job) {
      this.showInfoAlert('Información', 'Función de eliminación en desarrollo')
    },
    resetJobForm() {
      this.jobForm = {
        titulo: '',
        descripcion: '',
        sueldo: ''
      }
      this.errors = {}
    },
    validateState(field) {
      if (this.errors[field]) return false
      if (this.jobForm[field]) {
        const value = this.jobForm[field].toString().trim()
        switch (field) {
          case 'titulo': return value.length >= 3 ? true : null
          case 'descripcion': return value.length >= 10 ? true : null
          case 'sueldo': 
            const sueldo = parseFloat(value)
            return !isNaN(sueldo) && sueldo >= 0 ? true : null
          default: return null
        }
      }
      return null
    },
    formatSalary(salary) {
      return parseFloat(salary).toLocaleString('es-CO')
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('es-CO')
    },
    onJobsPageChanged(page) {
      this.loadJobs(page)
    },
    onJobsPerPageChanged(perPage) {
      // Actualizar el per_page en la paginación
      if (this.jobsPagination) {
        this.jobsPagination.per_page = perPage
      }
      this.loadJobs(1)
    },
    onJobApplicationsPageChanged(page) {
      this.loadJobApplications(page)
    },
    onJobApplicationsPerPageChanged(perPage) {
      // Actualizar el per_page en la paginación
      if (this.jobApplicationsPagination) {
        this.jobApplicationsPagination.per_page = perPage
      }
      this.loadJobApplications(1)
    }
  }
}
</script>

<style scoped>
.admin-jobs {
  min-height: 100vh;
  padding: 20px;
}
</style> 