<template>
  <div class="chart-container">
    <LineChartComponent 
      v-if="chartData && chartData.labels && chartData.datasets" 
      :chartData="chartData" 
      :chartOptions="chartOptions" 
    />
    <div v-else class="text-center text-muted">
      <i class="fas fa-chart-line fa-3x mb-3"></i>
      <p>No hay datos para mostrar</p>
    </div>
  </div>
</template>

<script>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

export default {
  name: 'LineChart',
  components: { 
    LineChartComponent: Line 
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