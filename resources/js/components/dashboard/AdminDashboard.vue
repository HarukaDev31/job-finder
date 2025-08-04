<template>
  <div class="admin-dashboard">
    <!-- Welcome Section -->
    <b-row class="mb-4">
      <b-col>
        <b-card bg-variant="light" border-variant="primary">
          <b-card-body class="text-center">
            <i class="fas fa-user-shield fa-3x text-primary mb-3"></i>
            <h3>Panel de Administración</h3>
            <p class="lead">Métricas y estadísticas del portal de trabajo</p>
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
            <i class="fas fa-paper-plane fa-2x mb-2"></i>
            <h4>{{ metrics.totalApplications || 0 }}</h4>
            <p class="mb-0">Total Postulaciones</p>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="3">
        <b-card bg-variant="info" text-variant="white" class="text-center">
          <b-card-body>
            <i class="fas fa-users fa-2x mb-2"></i>
            <h4>{{ metrics.totalUsers || 0 }}</h4>
            <p class="mb-0">Usuarios Registrados</p>
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

    <!-- Charts Section -->
    <b-row class="mb-4">
      <b-col md="8">
        <b-card>
          <b-card-header>
            <h5 class="mb-0">
              <i class="fas fa-chart-line mr-2"></i>
              Actividad Mensual
            </h5>
          </b-card-header>
          <b-card-body>
            <LineChart
              v-if="!chartsLoading && activityChartData && activityChartData.labels && activityChartData.labels.length > 0"
              :data="activityChartData" :options="activityChartOptions" />
            <div v-else-if="chartsLoading" class="text-center text-muted">
              <b-spinner></b-spinner>
              <p class="mt-2">Cargando gráfico...</p>
            </div>
            <div v-else class="text-center text-muted">
              <i class="fas fa-chart-line fa-3x mb-3"></i>
              <p>No hay datos para mostrar</p>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="4">
        <b-card>
          <b-card-header>
            <h5 class="mb-0">
              <i class="fas fa-chart-pie mr-2"></i>
              Estado de Postulaciones
            </h5>
          </b-card-header>
          <b-card-body>
            <DoughnutChart
              v-if="!chartsLoading && statusChartData && statusChartData.labels && statusChartData.labels.length > 0"
              :data="statusChartData" :options="statusChartOptions" />
            <div v-else-if="chartsLoading" class="text-center text-muted">
              <b-spinner></b-spinner>
              <p class="mt-2">Cargando gráfico...</p>
            </div>
            <div v-else class="text-center text-muted">
              <i class="fas fa-chart-pie fa-3x mb-3"></i>
              <p>No hay datos para mostrar</p>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Additional Charts Section -->
    <b-row class="mb-4">
      <b-col md="6">
        <b-card>
          <b-card-header>
            <h5 class="mb-0">
              <i class="fas fa-trophy mr-2"></i>
              Top 5 Trabajos con Más Postulaciones
            </h5>
          </b-card-header>
          <b-card-body>
            <BarChart
              v-if="!chartsLoading && topJobsChartData && topJobsChartData.labels && topJobsChartData.labels.length > 0"
              :data="topJobsChartData" :options="topJobsChartOptions" />
            <div v-else-if="chartsLoading" class="text-center text-muted">
              <b-spinner></b-spinner>
              <p class="mt-2">Cargando gráfico...</p>
            </div>
            <div v-else class="text-center text-muted">
              <i class="fas fa-trophy fa-3x mb-3"></i>
              <p>No hay trabajos para mostrar</p>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col md="6">
        <b-card>
          <b-card-header>
            <h5 class="mb-0">
              <i class="fas fa-user-plus mr-2"></i>
              Nuevos Usuarios por Mes
            </h5>
          </b-card-header>
          <b-card-body>
            <LineChart
              v-if="!chartsLoading && usersChartData && usersChartData.labels && usersChartData.labels.length > 0"
              :data="usersChartData" :options="usersChartOptions" />
            <div v-else-if="chartsLoading" class="text-center text-muted">
              <b-spinner></b-spinner>
              <p class="mt-2">Cargando gráfico...</p>
            </div>
            <div v-else class="text-center text-muted">
              <i class="fas fa-user-plus fa-3x mb-3"></i>
              <p>No hay datos para mostrar</p>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

    <!-- Quick Actions -->
    <b-row class="mb-4">
      <b-col>
        <b-card>
          <b-card-header>
            <h5 class="mb-0">
              <i class="fas fa-bolt mr-2"></i>
              Acciones Rápidas
            </h5>
          </b-card-header>
          <b-card-body>
            <div class="d-flex justify-content-center">
              <b-button variant="success" @click="goToJobs" class="mr-3">
                <i class="fas fa-briefcase mr-2"></i>
                Gestionar Trabajos
              </b-button>
              <b-button variant="info" @click="goToApplications" class="mr-3">
                <i class="fas fa-users mr-2"></i>
                Gestionar Postulaciones
              </b-button>
              <b-button variant="primary" @click="refreshData">
                <i class="fas fa-sync-alt mr-2"></i>
                Actualizar Datos
              </b-button>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>

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
import LineChart from '@/components/charts/LineChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'
import BarChart from '@/components/charts/BarChart.vue'
import dashboardService from '@/services/dashboardService'

export default {
  name: 'AdminDashboard',
  mixins: [alertMixin],
  components: {
    LineChart,
    DoughnutChart,
    BarChart
  },
  data() {
    return {
      loading: false,
      chartsLoading: true,
      metrics: {
        totalJobs: 0,
        totalApplications: 0,
        totalUsers: 0,
        pendingApplications: 0,
        jobsByMonth: [],
        applicationsByMonth: [],
        applicationsByStatus: {},
        topJobs: [],
        usersByMonth: []
      }
    }
  },
  computed: {
    // Datos para el gráfico de actividad mensual
    activityChartData() {
      const months = this.getMonthLabels()
      
      // Asegurar que siempre tengamos datos válidos
      const jobsData = this.metrics.jobsByMonth || []
      const applicationsData = this.metrics.applicationsByMonth || []

      return {
        labels: months,
        datasets: [
          {
            label: 'Trabajos',
            data: this.getMonthlyData(jobsData, months),
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            tension: 0.4
          },
          {
            label: 'Postulaciones',
            data: this.getMonthlyData(applicationsData, months),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4
          }
        ]
      }
    },
    activityChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    },
    // Datos para el gráfico de estado de postulaciones
    statusChartData() {
      const applicationsByStatus = this.metrics.applicationsByStatus || {}
      
      if (Object.keys(applicationsByStatus).length === 0) {
        return { labels: [], datasets: [] }
      }

      const statusLabels = {
        'pendiente': 'Pendiente',
        'revisado': 'En Revisión',
        'aceptado': 'Aceptada',
        'rechazado': 'Rechazada'
      }

      const colors = ['#ffc107', '#17a2b8', '#28a745', '#dc3545']

      return {
        labels: Object.keys(applicationsByStatus).map(key => statusLabels[key] || key),
        datasets: [{
          data: Object.values(applicationsByStatus),
          backgroundColor: colors.slice(0, Object.keys(applicationsByStatus).length),
          borderWidth: 2,
          borderColor: '#fff'
        }]
      }
    },
    statusChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    },
    // Datos para el gráfico de top trabajos
    topJobsChartData() {
      const topJobs = this.metrics.topJobs || []
      
      if (topJobs.length === 0) {
        return { labels: [], datasets: [] }
      }

      return {
        labels: topJobs.map(job => job.titulo.substring(0, 20) + (job.titulo.length > 20 ? '...' : '')),
        datasets: [{
          label: 'Postulaciones',
          data: topJobs.map(job => job.postulaciones_count),
          backgroundColor: 'rgba(54, 162, 235, 0.8)',
          borderColor: '#36a2eb',
          borderWidth: 1
        }]
      }
    },
    topJobsChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    },
    // Datos para el gráfico de usuarios por mes
    usersChartData() {
      const months = this.getMonthLabels()
      const usersByMonth = this.metrics.usersByMonth || []

      return {
        labels: months,
        datasets: [{
          label: 'Nuevos Usuarios',
          data: this.getMonthlyData(usersByMonth, months),
          borderColor: '#6f42c1',
          backgroundColor: 'rgba(111, 66, 193, 0.1)',
          tension: 0.4
        }]
      }
    },
    usersChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    }
  },
  mounted() {
    this.loadMetrics()
  },
  methods: {
    async loadMetrics() {
      this.chartsLoading = true
      try {
        const response = await dashboardService.getAdminMetrics()
        this.metrics = response.data || {}
        
        // Asegurar que todas las propiedades existan
        this.metrics = {
          totalJobs: this.metrics.totalJobs || 0,
          totalApplications: this.metrics.totalApplications || 0,
          totalUsers: this.metrics.totalUsers || 0,
          pendingApplications: this.metrics.pendingApplications || 0,
          jobsByMonth: this.metrics.jobsByMonth || [],
          applicationsByMonth: this.metrics.applicationsByMonth || [],
          applicationsByStatus: this.metrics.applicationsByStatus || {},
          topJobs: this.metrics.topJobs || [],
          usersByMonth: this.metrics.usersByMonth || []
        }
      } catch (error) {
        console.error('Error loading metrics:', error)
        this.showErrorAlert('Error', 'Error al cargar las métricas')
        
        // Establecer datos por defecto en caso de error
        this.metrics = {
          totalJobs: 0,
          totalApplications: 0,
          totalUsers: 0,
          pendingApplications: 0,
          jobsByMonth: [],
          applicationsByMonth: [],
          applicationsByStatus: {},
          topJobs: [],
          usersByMonth: []
        }
      } finally {
        this.chartsLoading = false
      }
    },
    // Métodos auxiliares para los gráficos
    getMonthLabels() {
      const months = []
      const monthNames = [
        'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
        'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
      ]

      for (let i = 5; i >= 0; i--) {
        const date = new Date()
        date.setMonth(date.getMonth() - i)
        months.push(monthNames[date.getMonth()])
      }

      return months
    },
    getMonthlyData(data, labels) {
      const result = new Array(6).fill(0)

      if (!Array.isArray(data) || data.length === 0) return result

      data.forEach(item => {
        if (!item || typeof item.month === 'undefined' || typeof item.year === 'undefined' || typeof item.count === 'undefined') {
          return
        }
        
        const monthIndex = item.month - 1
        const currentMonth = new Date().getMonth()
        const currentYear = new Date().getFullYear()

        // Calcular la diferencia en meses considerando el año
        let diff = (currentYear - item.year) * 12 + (currentMonth - monthIndex)

        if (diff >= 0 && diff < 6) {
          result[5 - diff] = item.count
        }
      })

      return result
    },
    goToJobs() {
      this.$router.push('/admin/jobs')
    },
    goToApplications() {
      this.$router.push('/admin/applications')
    },
    async refreshData() {
      this.loading = true
      try {
        await this.loadMetrics()
        this.showSuccessAlert('Éxito', 'Datos actualizados correctamente')
      } catch (error) {
        this.showErrorAlert('Error', 'Error al actualizar los datos')
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<style scoped>
.admin-dashboard {
  min-height: 100vh;
  padding: 20px;
}
</style>