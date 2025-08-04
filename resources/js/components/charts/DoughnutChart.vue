<template>
  <div class="chart-container">
    <DoughnutChartComponent 
      v-if="chartData && chartData.labels && chartData.datasets" 
      :chartData="chartData" 
      :chartOptions="chartOptions" 
    />
    <div v-else class="text-center text-muted">
      <i class="fas fa-chart-pie fa-3x mb-3"></i>
      <p>No hay datos para mostrar</p>
    </div>
  </div>
</template>

<script>
import { Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(
  ArcElement,
  Tooltip,
  Legend
)

export default {
  name: 'DoughnutChart',
  components: { 
    DoughnutChartComponent: Doughnut 
  },
  props: {
    data: {
      type: Object,
      default: () => ({ labels: [], datasets: [] })
    },
    options: {
      type: Object,
      default: () => ({})
    }
  },
  computed: {
    chartData() {
      // Validar que los datos existan y tengan la estructura correcta
      if (!this.data || !this.data.labels || !this.data.datasets) {
        return { labels: [], datasets: [] }
      }
      return this.data
    },
    chartOptions() {
      return this.options || {}
    }
  }
}
</script>

<style scoped>
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
}
</style> 