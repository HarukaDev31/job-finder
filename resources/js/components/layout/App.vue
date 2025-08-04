<template>
  <div id="app">
    <!-- Navigation -->
    <b-navbar toggleable="lg" type="dark" :variant="navbarVariant" class="fixed-top">
      <b-navbar-brand href="#" @click="goHome">
        <i class="fas fa-briefcase mr-2"></i>
        Job Finder
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <!-- Navigation for authenticated users -->
        <b-navbar-nav v-if="isAuthenticated">
          <!-- Admin Navigation -->
          <template v-if="isAdmin">
            <b-nav-item to="/admin/dashboard" exact>
              <i class="fas fa-tachometer-alt mr-1"></i>
              Dashboard
            </b-nav-item>
            <b-nav-item to="/admin/jobs">
              <i class="fas fa-briefcase mr-1"></i>
              Gestionar Trabajos
            </b-nav-item>
            <b-nav-item to="/admin/applications">
              <i class="fas fa-users mr-1"></i>
              Gestionar Postulaciones
            </b-nav-item>
          </template>
          
          <!-- Postulante Navigation -->
          <template v-else>
            <b-nav-item to="/dashboard" exact>
              <i class="fas fa-tachometer-alt mr-1"></i>
              Dashboard
            </b-nav-item>
            <b-nav-item to="/jobs">
              <i class="fas fa-search mr-1"></i>
              Buscar Trabajos
            </b-nav-item>
            <b-nav-item to="/applications">
              <i class="fas fa-paper-plane mr-1"></i>
              Mis Postulaciones
            </b-nav-item>
          </template>
        </b-navbar-nav>

        <!-- Navigation for guests -->
        <b-navbar-nav v-else>
          <b-nav-item to="/" exact>
            <i class="fas fa-home mr-1"></i>
            Inicio
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto">
          <!-- User Menu -->
          <b-nav-item-dropdown v-if="isAuthenticated" right>
            <template #button-content>
              <i class="fas fa-user-circle mr-1"></i>
              {{ userName }}
              <b-badge v-if="isAdmin" variant="warning" class="ml-1">Admin</b-badge>
            </template>
            <b-dropdown-item @click="logout">
              <i class="fas fa-sign-out-alt mr-1"></i>
              Cerrar Sesión
            </b-dropdown-item>
          </b-nav-item-dropdown>
          
          <!-- Guest Menu -->
          <template v-else>
            <b-nav-item to="/login">
              <i class="fas fa-sign-in-alt mr-1"></i>
              Iniciar Sesión
            </b-nav-item>
            <b-nav-item to="/register">
              <i class="fas fa-user-plus mr-1"></i>
              Registrarse
            </b-nav-item>
          </template>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>

    <!-- Main Content -->
    <div class="container-fluid main-content">
      <router-view @auth-updated="updateAuthState"></router-view>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <!-- Loading Overlay -->
    <div v-if="globalLoading" class="loading-overlay">
      <div class="loading-content">
        <b-spinner variant="primary" label="Cargando..."></b-spinner>
        <p class="mt-2">Cargando...</p>
      </div>
    </div>
  </div>
</template>

<script>
import authService from '@/services/auth.js'

export default {
  name: 'App',
  data() {
    return {
      globalLoading: false,
      authState: {
        isAuthenticated: false,
        user: null,
        isAdmin: false
      }
    }
  },
  computed: {
    isAuthenticated() {
      return this.authState.isAuthenticated
    },
    user() {
      return this.authState.user
    },
    userName() {
      return this.user ? this.user.name : 'Usuario'
    },
    isAdmin() {
      return this.authState.isAdmin
    },
    navbarVariant() {
      if (!this.isAuthenticated) return 'primary'
      return this.isAdmin ? 'primary' : 'success'
    }
  },
  methods: {
    async logout() {
      this.globalLoading = true
      try {
        await authService.logout()
        this.$router.push('/')
        this.$bvToast.toast('Sesión cerrada exitosamente', {
          title: 'Éxito',
          variant: 'success',
          solid: true
        })
      } catch (error) {
        console.error('Error al cerrar sesión:', error)
        this.$bvToast.toast('Error al cerrar sesión', {
          title: 'Error',
          variant: 'danger',
          solid: true
        })
      } finally {
        this.globalLoading = false
      }
    },
    updateAuthState(userData) {
      // El estado se actualiza automáticamente a través del servicio
      this.$nextTick(() => {
        // Redirigir según el rol después del login/registro
        if (userData.role === 'admin') {
          this.$router.push('/admin/dashboard')
        } else {
          this.$router.push('/dashboard')
        }
      })
    },
    goHome() {
      if (this.isAuthenticated) {
        if (this.isAdmin) {
          this.$router.push('/admin/dashboard')
        } else {
          this.$router.push('/dashboard')
        }
      } else {
        this.$router.push('/')
      }
    },
    showGlobalLoading() {
      this.globalLoading = true
    },
    hideGlobalLoading() {
      this.globalLoading = false
    },
    // Método para manejar cambios en el estado de autenticación
    handleAuthChange(authData) {
      this.authState = {
        isAuthenticated: authData.isAuthenticated,
        user: authData.user,
        isAdmin: authData.isAdmin
      }
    }
  },
  mounted() {
    // Configurar axios con el token CSRF para rutas que lo necesiten
    if (window.appConfig?.csrfToken) {
      axios.defaults.headers.common['X-CSRF-TOKEN'] = window.appConfig.csrfToken
    }

    // Inicializar el estado de autenticación
    this.authState = {
      isAuthenticated: authService.isAuthenticated(),
      user: authService.getUser(),
      isAdmin: authService.isAdmin()
    }

    // Suscribirse a cambios en el estado de autenticación
    this.unsubscribe = authService.subscribe(this.handleAuthChange)

    // Verificar si hay un token válido al cargar la app
    if (authService.isAuthenticated()) {
      authService.getCurrentUser().catch(() => {
        // Si no se puede obtener el usuario, limpiar la sesión
        authService.logout()
        this.$router.push('/login')
      })
    }
  },
  beforeDestroy() {
    // Limpiar la suscripción cuando el componente se destruye
    if (this.unsubscribe) {
      this.unsubscribe()
    }
  },
  provide() {
    return {
      showGlobalLoading: this.showGlobalLoading,
      hideGlobalLoading: this.hideGlobalLoading
    }
  }
}
</script>

<style>
#app {
  min-height: 100vh;
  background-color: #f8f9fa;
}

.container-fluid {
  padding: 0 20px;
}

.main-content {
  padding-top: 80px; /* Espacio para el navbar fijo */
  min-height: calc(100vh - 80px); /* Altura mínima considerando el navbar */
}

#toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
}

.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 10000;
}

.loading-content {
  background-color: white;
  padding: 2rem;
  border-radius: 8px;
  text-align: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}
</style> 