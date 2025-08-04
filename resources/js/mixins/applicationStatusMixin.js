export default {
  methods: {
    getStatusVariant(status) {
      const variants = {
        'pendiente': 'warning',
        'aceptado': 'success',
        'rechazado': 'danger',
        'revisado': 'info'
      }
      return variants[status] || 'secondary'
    },

    getStatusText(status) {
      const texts = {
        'pendiente': 'Pendiente',
        'aceptado': 'Aceptado',
        'rechazado': 'Rechazado',
        'revisado': 'En Revisión'
      }
      return texts[status] || 'Desconocido'
    }
  }
} 