<template>
  <div class="register-container">
    <b-row class="justify-content-center w-100">
      <b-col md="8" lg="6">
        <div class="register-card">
          <div class="register-header">
            <div class="register-icon">
              <i class="fas fa-user-plus"></i>
            </div>
            <h2>Registro de Postulante</h2>
            <p>Crea tu cuenta para acceder al portal de trabajo</p>
          </div>

          <validation-observer ref="observer" v-slot="{ handleSubmit }">
            <b-form @submit.prevent="handleSubmit(onSubmit)" v-if="!loading" class="register-form">
              <b-row>
                <!-- Document Type -->
                <b-col md="6">
                  <b-form-group label="Tipo de Documento:" label-for="tipo_documento" class="w-100">
                    <validation-provider name="tipo de documento" rules="required" v-slot="{ errors }">
                      <b-form-select id="tipo_documento" label-cols="12" label-cols-lg="4" label-align="right"
                        label-align-lg="right" label-size="sm" label-class="font-weight-bold" label-for="tipo_documento"
                        v-model="form.tipo_documento" :options="documentTypes" :state="errors.length === 0" required
                        :disabled="loading" class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!-- Document Number -->
                <b-col md="6">
                  <b-form-group label="Número de Documento:" label-for="numero_documento">
                    <validation-provider name="número de documento" rules="required" v-slot="{ errors }">
                      <b-form-input id="numero_documento" v-model="form.numero_documento" type="text"
                        :state="errors.length === 0" placeholder="12345678" required :disabled="loading"
                        class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-row>
                <!-- Names -->
                <b-col md="6">
                  <b-form-group label="Nombres:" label-for="nombres">
                    <validation-provider name="nombres" rules="required|min:2" v-slot="{ errors }">
                      <b-form-input id="nombres" v-model="form.nombres" type="text" :state="errors.length === 0"
                        placeholder="Juan Carlos" required :disabled="loading" class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!-- Surnames -->
                <b-col md="6">
                  <b-form-group label="Apellidos:" label-for="apellidos">
                    <validation-provider name="apellidos" rules="required|min:2" v-slot="{ errors }">
                      <b-form-input id="apellidos" v-model="form.apellidos" type="text" :state="errors.length === 0"
                        placeholder="García López" required :disabled="loading" class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-row>
                <!-- Birth Date -->
                <b-col md="6">
                  <b-form-group label="Fecha de Nacimiento:" label-for="fecha_nacimiento">
                    <validation-provider name="fecha de nacimiento" rules="required" v-slot="{ errors }">
                      <b-form-input id="fecha_nacimiento" v-model="form.fecha_nacimiento" type="date"
                        :state="errors.length === 0" required :disabled="loading" class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!-- Email -->
                <b-col md="6">
                  <b-form-group label="Correo Electrónico:" label-for="email">
                    <validation-provider name="email" rules="required|email" v-slot="{ errors }">
                      <b-form-input id="email" v-model="form.email" type="email" :state="errors.length === 0"
                        placeholder="tu@email.com" required :disabled="loading" class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>

              <b-row>
                <!-- Password -->
                <b-col md="6">
                  <b-form-group label="Contraseña:" label-for="password">
                                         <validation-provider name="password" rules="required|min:6" v-slot="{ errors }">
                      <b-form-input id="password" v-model="form.password" type="password" :state="errors.length === 0"
                        placeholder="Mínimo 6 caracteres" required :disabled="loading" class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>

                <!-- Confirm Password -->
                <b-col md="6">
                  <b-form-group label="Confirmar Contraseña:" label-for="password_confirmation">
                    <validation-provider name="password_confirmation" rules="required|confirmed:password"
                      v-slot="{ errors }">
                      <b-form-input id="password_confirmation" v-model="form.password_confirmation" type="password"
                        :state="errors.length === 0" placeholder="Repite tu contraseña" required :disabled="loading"
                        class="w-100" />
                      <b-form-invalid-feedback :state="errors.length === 0">
                        {{ errors[0] }}
                      </b-form-invalid-feedback>
                    </validation-provider>
                  </b-form-group>
                </b-col>
              </b-row>

              <!-- Submit Button -->
              <b-button type="submit" variant="success" block :disabled="loading">
                <b-spinner small v-if="loading"></b-spinner>
                <i v-else class="fas fa-user-plus mr-2"></i>
                {{ loading ? 'Creando cuenta...' : 'Crear Cuenta' }}
              </b-button>
            </b-form>
          </validation-observer>

          <!-- Loading State -->
          <div v-if="loading" class="loading-state">
            <b-spinner variant="success" label="Creando cuenta..."></b-spinner>
            <p>Creando tu cuenta...</p>
          </div>

          <!-- Links -->
          <div class="register-links">
            <p>
              ¿Ya tienes cuenta?
              <router-link to="/login" class="link-primary">
                Inicia sesión aquí
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
  name: 'Register',
  mixins: [alertMixin],
  data() {
    return {
      form: {
        tipo_documento: '',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      documentTypes: [
        { value: '', text: 'Selecciona un tipo' },
        { value: 'CC', text: 'Cédula de Ciudadanía' },
        { value: 'CE', text: 'Cédula de Extranjería' },
        { value: 'TI', text: 'Tarjeta de Identidad' },
        { value: 'PP', text: 'Pasaporte' }
      ],
      loading: false
    }
  },
  methods: {
    async onSubmit() {
      this.loading = true

      try {
        const result = await authService.register(this.form)

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
            this.showErrorAlert('Error', result.message || 'Error en el registro')
          }
        }
      } catch (error) {
        console.error('Error en registro:', error)

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
            this.showErrorAlert('Error', 'Error en el registro. Por favor, intente nuevamente.')
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
        tipo_documento: '',
        numero_documento: '',
        nombres: '',
        apellidos: '',
        fecha_nacimiento: '',
        email: '',
        password: '',
        password_confirmation: ''
      }
      // Resetear validación de VeeValidate
      this.$refs.observer.reset()
    }
  }
}
</script>

<style scoped>
.register-container {
  min-height: 80vh;
  display: flex;
  align-items: center;
  padding: 2rem 0;
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.register-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
  padding: 3rem 2rem;
  border: none;
  position: relative;
  overflow: hidden;
}

.register-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #28a745, #20c997);
}

.register-header {
  text-align: center;
  margin-bottom: 2rem;
}

.register-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #28a745, #20c997);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.register-icon i {
  font-size: 2.5rem;
  color: white;
}

.register-header h2 {
  color: #2d3748;
  font-weight: 700;
  margin-bottom: 0.5rem;
  font-size: 1.8rem;
}

.register-header p {
  color: #718096;
  margin: 0;
  font-size: 0.95rem;
}

.register-form {
  margin-bottom: 2rem;
}

.form-row {
  display: flex;
  flex-wrap: wrap;
  margin-right: -0.75rem;
  margin-left: -0.75rem;
}

.form-group {
  flex: 0 0 50%;
  max-width: 100%;
  padding-right: 0.75rem;
  padding-left: 0.75rem;
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
  border-color: #28a745;
  box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
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

.btn-success {
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
}

.btn-success:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.btn-success:disabled {
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

.register-links {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.register-links p {
  margin-bottom: 1rem;
  color: #718096;
}

.link-primary {
  color: #28a745;
  text-decoration: none;
  font-weight: 600;
}

.link-primary:hover {
  color: #218838;
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
  .register-container {
    padding: 1rem 0;
  }

  .register-card {
    padding: 2rem 1.5rem;
    margin: 0 1rem;
  }

  .form-group {
    flex: 0 0 100%;
    max-width: 100%;
  }

  .register-icon {
    width: 60px;
    height: 60px;
  }

  .register-icon i {
    font-size: 2rem;
  }

  .register-header h2 {
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
  outline: 2px solid #28a745;
  outline-offset: 2px;
}

/* Disabled state styles */
.form-control:disabled {
  background-color: #f7fafc;
  cursor: not-allowed;
  opacity: 0.6;
}
</style>