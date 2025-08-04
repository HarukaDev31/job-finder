export default {
  data() {
    return {
      showAlert: false,
      alertType: 'info',
      alertTitle: '',
      alertMessage: '',
      alertIcon: ''
    }
  },
  
  methods: {
    showSuccessAlert(title, message) {
      this.showAlert = true
      this.alertType = 'success'
      this.alertTitle = title || 'Éxito'
      this.alertMessage = message
      this.alertIcon = 'fas fa-check-circle'
    },
    
    showErrorAlert(title, message) {
      this.showAlert = true
      this.alertType = 'danger'
      this.alertTitle = title || 'Error'
      this.alertMessage = message
      this.alertIcon = 'fas fa-exclamation-triangle'
    },
    
    showWarningAlert(title, message) {
      this.showAlert = true
      this.alertType = 'warning'
      this.alertTitle = title || 'Advertencia'
      this.alertMessage = message
      this.alertIcon = 'fas fa-exclamation-circle'
    },
    
    showInfoAlert(title, message) {
      this.showAlert = true
      this.alertType = 'info'
      this.alertTitle = title || 'Información'
      this.alertMessage = message
      this.alertIcon = 'fas fa-info-circle'
    },
    
    showValidationError(errors) {
      let message = 'Por favor, corrige los siguientes errores:'
      
      if (typeof errors === 'object') {
        const errorMessages = []
        Object.keys(errors).forEach(field => {
          if (Array.isArray(errors[field])) {
            errorMessages.push(...errors[field])
          } else {
            errorMessages.push(errors[field])
          }
        })
        
        if (errorMessages.length > 0) {
          message += '<ul class="mt-2 mb-0">'
          errorMessages.forEach(error => {
            message += `<li>${error}</li>`
          })
          message += '</ul>'
        }
      } else if (typeof errors === 'string') {
        message = errors
      }
      
      this.showErrorAlert('Error de Validación', message)
    },
    
    closeAlert() {
      this.showAlert = false
      this.alertTitle = ''
      this.alertMessage = ''
      this.alertIcon = ''
    },
    
    showAutoCloseAlert(type, title, message, duration = 5000) {
      this[`show${type.charAt(0).toUpperCase() + type.slice(1)}Alert`](title, message)
      
      setTimeout(() => {
        this.closeAlert()
      }, duration)
    }
  }
} 