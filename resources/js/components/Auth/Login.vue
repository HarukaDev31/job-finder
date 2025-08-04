<template>
  <div class="login-container w-100">
    <b-row class="justify-content-center w-100">
      <b-col md="6" lg="6">
        <div class="login-card">
          <div class="login-header">
            <div class="login-icon">
              <i class="fas fa-user-circle"></i>
            </div>
            <h2>Iniciar Sesión</h2>
            <p>Accede a tu cuenta para continuar</p>
          </div>

          <validation-observer ref="observer" v-slot="{ handleSubmit }">
            <b-form @submit.prevent="handleSubmit(onSubmit)" v-if="!loading" class="login-form">
              <!-- Email Field -->
              <b-form-group label="Correo Electrónico:" label-for="email">
                <validation-provider name="email" rules="required|email" v-slot="{ errors }">
                  <b-form-input
                    id="email"
                    v-model="form.email"
                    type="email"
                    :state="errors.length === 0"
                    placeholder="tu@email.com"
                    required
                    :disabled="loading"
                  />
                  <b-form-invalid-feedback :state="errors.length === 0">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </validation-provider>
              </b-form-group>

              <!-- Password Field -->
              <b-form-group label="Contraseña:" label-for="password">
                <validation-provider name="contraseña" rules="required" v-slot="{ errors }">
                  <b-form-input
                    id="password"
                    v-model="form.password"
                    type="password"
                    :state="errors.length === 0"
                    placeholder="Tu contraseña"
                    required
                    :disabled="loading"
                  />
                  <b-form-invalid-feedback :state="errors.length === 0">
                    {{ errors[0] }}
                  </b-form-invalid-feedback>
                </validation-provider>
              </b-form-group>

              <!-- Submit Button -->
              <b-button
                type="submit"
                variant="primary"
                block
                :disabled="loading"
              >
                <b-spinner small v-if="loading"></b-spinner>
                <i v-else class="fas fa-sign-in-alt mr-2"></i>
                {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
              </b-button>
            </b-form>
          </validation-observer>

          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <b-spinner variant="primary" label="Iniciando sesión..."></b-spinner>
            <p>Iniciando sesión...</p>
          </div>

          <!-- Links -->
          <div class="login-links">
            <p>
              ¿No tienes cuenta?
              <router-link to="/register" class="link-primary">
                Regístrate aquí
              </router-link>
            </p>
            <router-link to="/" class="link-secondary">
              <i class="fas fa-arrow-left mr-1"></i>
              Volver al inicio
            </router-link>
          </div>
        </div>
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
import authService from '@/services/auth.js'
import alertMixin from '@/mixins/alertMixin.js'

export default {
  name: 'Login',
  mixins: [alertMixin],
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      loading: false
    }
  },
  methods: {
    async onSubmit() {
      this.loading = true

      try {
        const result = await authService.login(this.form)
        
        if (result.success) {
          this.showSuccessAlert('Éxito', result.message)
          
          // Limpiar formulario después del éxito
          this.resetForm()
          
          // Redirigir según el rol (el estado ya se actualizó automáticamente)
          this.$nextTick(() => {
            if (result.user.role === 'admin') {
              this.$router.push('/admin/dashboard')
            } else {
              this.$router.push('/dashboard')
            }
          })
        } else {
          // Manejar errores de validación del backend
          if (result.errors) {
            this.showValidationError(result.errors)
          } else {
            this.showErrorAlert('Error', result.message || 'Error en el inicio de sesión')
          }
        }
      } catch (error) {
        console.error('Error en login:', error)
        
        // Manejar diferentes tipos de errores
        if (error.response && error.response.data) {
          const responseData = error.response.data
          
          if (responseData.errors) {
            // Errores de validación del backend
            this.showValidationError(responseData.errors)
          } else if (responseData.message) {
            // Mensaje de error específico
            this.showErrorAlert('Error', responseData.message)
          } else {
            // Error genérico
            this.showErrorAlert('Error', 'Error en el inicio de sesión. Por favor, intente nuevamente.')
          }
        } else {
          // Error de conexión
          this.showErrorAlert('Error de Conexión', 'No se pudo conectar con el servidor. Verifique su conexión a internet e intente nuevamente.')
        }
      } finally {
        this.loading = false
      }
    },
    resetForm() {
      this.form = {
        email: '',
        password: ''
      }
      // Resetear validación de VeeValidate
      this.$refs.observer.reset()
    }
  }
}
</script>

<style scoped>
.login-container {
  min-height: 80vh;
  display: flex;
  justify-content: center;
  align-content: center;
  width: 100%;
  align-items: center;
  padding: 2rem 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.login-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  padding: 3rem 2rem;
  border: none;
  width: 100%;
  margin: 0 auto;
  position: relative;
  overflow: hidden;
}

.login-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #667eea, #764ba2);
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.login-icon i {
  font-size: 2.5rem;
  color: white;
}

.login-header h2 {
  color: #2d3748;
  font-weight: 700;
  margin-bottom: 0.5rem;
  font-size: 1.8rem;
}

.login-header p {
  color: #718096;
  margin: 0;
  font-size: 0.95rem;
}

.login-form {
  margin-bottom: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  margin-bottom: 0.5rem;
  color: #2d3748;
  font-weight: 600;
  font-size: 0.9rem;
}

.form-control {
  width: 100%;
  padding: 0.75rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background-color: #f7fafc;
}

.form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  background-color: white;
}

.form-control.is-invalid {
  border-color: #e53e3e;
  box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #e53e3e;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: 600;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  width: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-primary:disabled {
  background: #cbd5e0;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-block {
  width: 100%;
}

.loading-state {
  text-align: center;
  padding: 2rem 0;
}

.loading-state p {
  margin-top: 1rem;
  color: #718096;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 10px;
  border: none;
  display: flex;
  align-items: center;
}

.alert-danger {
  background-color: #fed7d7;
  color: #c53030;
}

.alert-success {
  background-color: #c6f6d5;
  color: #2f855a;
}

.alert-dismissible {
  position: relative;
  padding-right: 3rem;
}

.close {
  position: absolute;
  top: 0;
  right: 0;
  padding: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: inherit;
  cursor: pointer;
}

.login-links {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.login-links p {
  margin-bottom: 1rem;
  color: #718096;
}

.link-primary {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.link-primary:hover {
  color: #5a67d8;
  text-decoration: underline;
}

.link-secondary {
  color: #718096;
  text-decoration: none;
  font-size: 0.9rem;
}

.link-secondary:hover {
  color: #4a5568;
  text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .login-container {
    padding: 1rem 0;
  }
  
  .login-card {
    padding: 2rem 1.5rem;
  }
  
  .login-icon {
    width: 60px;
    height: 60px;
  }
  
  .login-icon i {
    font-size: 2rem;
  }
  
  .login-header h2 {
    font-size: 1.5rem;
  }
}

/* Animation for form elements */
.form-control,
.btn {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Focus styles for accessibility */
.form-control:focus,
.btn:focus {
  outline: 2px solid #667eea;
  outline-offset: 2px;
}

/* Disabled state styles */
.form-control:disabled {
  background-color: #f7fafc;
  cursor: not-allowed;
  opacity: 0.6;
}
</style> 