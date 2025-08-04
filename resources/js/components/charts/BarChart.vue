<template>
  <div class="chart-container">
    <BarChartComponent 
      v-if="chartData && chartData.labels && chartData.datasets" 
      :chartData="chartData" 
      :chartOptions="chartOptions" 
    />
    <div v-else class="text-center text-muted">
      <i class="fas fa-chart-bar fa-3x mb-3"></i>
      <p>No hay datos para mostrar</p>
    </div>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
)

export default {
  name: 'BarChart',
  components: { 
    BarChartComponent: Bar 
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