export default {
  data() {
    return {
      statusOptions: [
        { value: '', text: 'Todos los estados' },
        { value: 'pendiente', text: 'Pendiente' },
        { value: 'revisado', text: 'En Revisión' },
        { value: 'aceptado', text: 'Aceptado' },
        { value: 'rechazado', text: 'Rechazado' }
      ]
    }
  }
} 