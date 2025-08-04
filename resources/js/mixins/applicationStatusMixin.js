export default {
  data() {
    return {
      statusOptions: [
        { value: '', text: 'Todos los estados' },
        { value: 'pendiente', text: 'Pendiente' },
        { value: 'revisado', text: 'Revisado' },
        { value: 'aceptado', text: 'Aceptado' },
        { value: 'rechazado', text: 'Rechazado' }
      ]
    }
  },
  methods: {
    getStatusVariant(status) {
      const variants = {
        'pendiente': 'warning',
        'revisado': 'info',
        'aceptado': 'success',
        'rechazado': 'danger'
      }
      return variants[status] || 'secondary'
    },
    getStatusText(status) {
      const texts = {
        'pendiente': 'Pendiente',
        'revisado': 'Revisado',
        'aceptado': 'Aceptado',
        'rechazado': 'Rechazado'
      }
      return texts[status] || status
    },
    getStatusIcon(status) {
      const icons = {
        'pendiente': 'fas fa-clock',
        'revisado': 'fas fa-eye',
        'aceptado': 'fas fa-check-circle',
        'rechazado': 'fas fa-times-circle'
      }
      return icons[status] || 'fas fa-question-circle'
    },
    getStatusColor(status) {
      const colors = {
        'pendiente': '#ffc107',
        'revisado': '#17a2b8',
        'aceptado': '#28a745',
        'rechazado': '#dc3545'
      }
      return colors[status] || '#6c757d'
    }
  }
} 