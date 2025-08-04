import axios from './interceptors'
import API_CONFIG from '../config/api.js'

class AuthService {
  constructor() {
    this.token = localStorage.getItem('jwt_token')
    this.user = JSON.parse(localStorage.getItem('user') || 'null')
    this.listeners = []
    
    if (this.token) {
      this.setAuthHeader(this.token)
    }
  }

  // Método para suscribirse a cambios de autenticación
  subscribe(callback) {
    this.listeners.push(callback)
    return () => {
      const index = this.listeners.indexOf(callback)
      if (index > -1) {
        this.listeners.splice(index, 1)
      }
    }
  }

  // Método para notificar a todos los listeners
  notifyListeners() {
    this.listeners.forEach(callback => {
      try {
        callback({
          isAuthenticated: this.isAuthenticated(),
          user: this.user,
          isAdmin: this.isAdmin()
        })
      } catch (error) {
        console.error('Error en listener de autenticación:', error)
      }
    })
  }

  setAuthHeader(token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  }

  clearAuthHeader() {
    delete axios.defaults.headers.common['Authorization']
  }

  async login(credentials) {
    try {
      const response = await axios.post('/auth/login', credentials)
      
      if (response.data.success) {
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('jwt_token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        this.setAuthHeader(this.token)
        
        // Notificar a todos los listeners
        this.notifyListeners()
        
        return {
          success: true,
          user: this.user,
          message: response.data.message
        }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Error de conexión',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  async register(userData) {
    try {
      const response = await axios.post('/auth/register', userData)
      
      if (response.data.success) {
        this.token = response.data.token
        this.user = response.data.user
        
        localStorage.setItem('jwt_token', this.token)
        localStorage.setItem('user', JSON.stringify(this.user))
        this.setAuthHeader(this.token)
        
        // Notificar a todos los listeners
        this.notifyListeners()
        
        return {
          success: true,
          user: this.user,
          message: response.data.message
        }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Error de conexión',
        errors: error.response?.data?.errors || {}
      }
    }
  }

  async logout() {
    try {
      if (this.token) {
        await axios.post('/auth/logout')
      }
    } catch (error) {
      console.error('Error al cerrar sesión:', error)
    } finally {
      this.token = null
      this.user = null
      localStorage.removeItem('jwt_token')
      localStorage.removeItem('user')
      this.clearAuthHeader()
      
      // Notificar a todos los listeners
      this.notifyListeners()
    }
  }

  async refreshToken() {
    // Si no hay token, no intentar refrescar
    if (!this.token) {
      console.log('No hay token para refrescar')
      return false
    }

    try {
      // Usar axios directamente sin interceptores para evitar bucles
      const response = await axios.post('/auth/refresh', {}, {
        // Agregar flag para evitar que el interceptor procese esta petición
        _skipAuthRefresh: true
      })
      
      if (response.data.success) {
        this.token = response.data.token
        localStorage.setItem('jwt_token', this.token)
        this.setAuthHeader(this.token)
        
        // Notificar a todos los listeners
        this.notifyListeners()
        
        console.log('Token refrescado exitosamente')
        return true
      } else {
        console.log('Respuesta de refresh no exitosa:', response.data)
        return false
      }
    } catch (error) {
      console.error('Error al refrescar token:', error.response?.status, error.response?.data)
      
      // Si el error es 401, significa que el refresh token también expiró
      if (error.response?.status === 401) {
        console.log('Refresh token expirado, limpiando sesión')
        this.logout()
        return false
      }
      
      // Para otros errores, también limpiar sesión por seguridad
      this.logout()
      return false
    }
  }

  async getCurrentUser() {
    try {
      const response = await axios.get('/auth/me')
      
      if (response.data.success) {
        this.user = response.data.user
        localStorage.setItem('user', JSON.stringify(this.user))
        
        // Notificar a todos los listeners
        this.notifyListeners()
        
        return this.user
      }
    } catch (error) {
      console.error('Error al obtener usuario actual:', error)
      return null
    }
  }

  isAuthenticated() {
    return !!this.token && !!this.user && !this.isTokenExpired()
  }

  isAdmin() {
    return this.user && this.user.role === 'admin'
  }

  isPostulante() {
    return this.user && this.user.role === 'postulante'
  }

  getUser() {
    return this.user
  }

  getToken() {
    return this.token
  }

  // Método para verificar si el token está expirado
  isTokenExpired() {
    if (!this.token) return true
    
    try {
      const payload = JSON.parse(atob(this.token.split('.')[1]))
      const currentTime = Date.now() / 1000
      return payload.exp < currentTime
    } catch (error) {
      console.error('Error al verificar token:', error)
      return true
    }
  }

  // Método para verificar si el token necesita ser refrescado
  shouldRefreshToken() {
    if (!this.token) return false
    
    try {
      const payload = JSON.parse(atob(this.token.split('.')[1]))
      const currentTime = Date.now() / 1000
      const refreshTime = payload.exp - (API_CONFIG.jwt.refreshBeforeExpiry * 60)
      return currentTime >= refreshTime
    } catch (error) {
      console.error('Error al verificar si debe refrescar token:', error)
      return true
    }
  }
}

// Crear instancia del servicio
const authService = new AuthService()

// Variable para controlar el refresh en progreso
let isRefreshing = false
let failedQueue = []
let refreshAttempts = 0
const MAX_REFRESH_ATTEMPTS = 3

const processQueue = (error, token = null) => {
  failedQueue.forEach(prom => {
    if (error) {
      prom.reject(error)
    } else {
      prom.resolve(token)
    }
  })
  
  failedQueue = []
}

// Interceptor para manejar errores de token expirado
axios.interceptors.request.use(
  (config) => {
    // No agregar token para rutas de autenticación o si es un refresh
    const authRoutes = ['/auth/login', '/auth/register', '/auth/refresh']
    const isAuthRoute = authRoutes.some(route => config.url.includes(route))
    
    if (!isAuthRoute && !config._skipAuthRefresh && authService.token) {
      config.headers.Authorization = `Bearer ${authService.token}`
    }
    
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

axios.interceptors.response.use(
  (response) => response,
  async (error) => {
    const originalRequest = error.config

    // Si es un error 401 (no autorizado) y no hemos intentado refrescar el token
    // Y no es una petición de refresh
    if (error.response?.status === 401 && !originalRequest._retry && !originalRequest._skipAuthRefresh) {
      
      // Si ya estamos refrescando, agregar la petición a la cola
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          failedQueue.push({ resolve, reject })
        }).then(token => {
          originalRequest.headers.Authorization = `Bearer ${token}`
          return axios(originalRequest)
        }).catch(err => {
          return Promise.reject(err)
        })
      }

      originalRequest._retry = true
      isRefreshing = true
      refreshAttempts++

      // Verificar si hemos excedido el número máximo de intentos
      if (refreshAttempts > MAX_REFRESH_ATTEMPTS) {
        console.log('Máximo número de intentos de refresh alcanzado')
        isRefreshing = false
        refreshAttempts = 0
        authService.logout()
        if (window.location.pathname !== '/login') {
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }

      // Verificar si el token está expirado o necesita refrescarse
      if (authService.isTokenExpired() || authService.shouldRefreshToken()) {
        try {
          const refreshed = await authService.refreshToken()
          
          if (refreshed) {
            // Resetear contador de intentos
            refreshAttempts = 0
            
            // Procesar la cola de peticiones fallidas
            processQueue(null, authService.token)
            
            // Reintentar la petición original con el nuevo token
            originalRequest.headers.Authorization = `Bearer ${authService.token}`
            return axios(originalRequest)
          } else {
            // Si no se pudo refrescar, procesar la cola con error
            processQueue(new Error('Token refresh failed'), null)
            
            // Limpiar sesión y redirigir
            authService.logout()
            if (window.location.pathname !== '/login') {
              window.location.href = '/login'
            }
          }
        } catch (refreshError) {
          // Si hay error en el refresh, procesar la cola con error
          processQueue(refreshError, null)
          
          // Limpiar sesión y redirigir
          authService.logout()
          if (window.location.pathname !== '/login') {
            window.location.href = '/login'
          }
        } finally {
          isRefreshing = false
          // No resetear refreshAttempts aquí para mantener el conteo de intentos fallidos
        }
      } else {
        // Si el token no está expirado pero recibimos 401, limpiar sesión
        isRefreshing = false
        refreshAttempts = 0 // Resetear contador ya que no es un problema de refresh
        authService.logout()
        if (window.location.pathname !== '/login') {
          window.location.href = '/login'
        }
      }
    }

    return Promise.reject(error)
  }
)

export default authService 