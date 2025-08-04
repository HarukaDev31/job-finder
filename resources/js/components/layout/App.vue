<template>
  <div id="app">
    <b-navbar toggleable="lg" type="dark" :variant="navbarVariant" class="fixed-top">
      <b-navbar-brand href="#" @click="goHome">
        <i class="fas fa-briefcase mr-2"></i>
        Job Finder
      </b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav v-if="isAuthenticated">
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

        <b-navbar-nav v-else>
          <b-nav-item to="/" exact>
            <i class="fas fa-home mr-1"></i>
            Inicio
          </b-nav-item>
        </b-navbar-nav>

        <b-navbar-nav class="ml-auto">
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

    <div class="container-fluid main-content">
      <router-view @auth-updated="updateAuthState"></router-view>
    </div>

    <div id="toast-container"></div>

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
      this.$nextTick(() => {
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
    handleAuthChange(authData) {
      this.authState = {
        isAuthenticated: authData.isAuthenticated,
        user: authData.user,
        isAdmin: authData.isAdmin
      }
    }
  },
  mounted() {
    if (window.appConfig?.csrfToken) {
      axios.defaults.headers.common['X-CSRF-TOKEN'] = window.appConfig.csrfToken
    }

    this.authState = {
      isAuthenticated: authService.isAuthenticated(),
      user: authService.getUser(),
      isAdmin: authService.isAdmin()
    }

    this.unsubscribe = authService.subscribe(this.handleAuthChange)

    if (authService.isAuthenticated()) {
      this.globalLoading = true
      
      authService.getCurrentUser()
        .then(user => {
          if (user) {
            console.log('Usuario verificado:', user)
          }
        })
        .catch(error => {
          console.error('Error al verificar usuario:', error)
          authService.logout()
          this.$router.push('/login')
        })
        .finally(() => {
          this.globalLoading = false
        })
    }
  },
  beforeDestroy() {
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
  padding-top: 80px;
  min-height: calc(100vh - 80px);
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
  z-index: 9999;
}

.loading-content {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  text-align: center;
}

#toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
}
</style> 