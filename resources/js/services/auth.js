import axios from './interceptors'
import API_CONFIG from '../config/api.js'

class AuthService {
  constructor() {
    this.token = localStorage.getItem('jwt_token')
    this.user = JSON.parse(localStorage.getItem('user') || 'null')
    this.listeners = []
    
    this.initializeAuth()
  }

  async initializeAuth() {
    if (this.token && this.user) {
      if (this.isTokenExpired()) {
        console.log('Token expirado al inicializar, intentando refrescar...')
        const refreshed = await this.refreshToken()
        if (!refreshed) {
          this.logout()
          return
        }
      } else if (this.shouldRefreshToken()) {
        console.log('Token necesita refrescarse, refrescando...')
        await this.refreshToken()
      }
      
      this.setAuthHeader(this.token)
      
      try {
        await this.getCurrentUser()
      } catch (error) {
        console.log('Error al verificar usuario actual, limpiando sesión')
        this.logout()
      }
    }
  }

  subscribe(callback) {
    this.listeners.push(callback)
    return () => {
      const index = this.listeners.indexOf(callback)
      if (index > -1) {
        this.listeners.splice(index, 1)
      }
    }
  }

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
      
      this.notifyListeners()
    }
  }

  async refreshToken() {
    if (!this.token) {
      console.log('No hay token para refrescar')
      return false
    }

    try {
      const response = await axios.post('/auth/refresh', {}, {
        _skipAuthRefresh: true
      })
      
      if (response.data.success) {
        this.token = response.data.token
        localStorage.setItem('jwt_token', this.token)
        this.setAuthHeader(this.token)
        
        this.notifyListeners()
        
        console.log('Token refrescado exitosamente')
        return true
      } else {
        console.log('Respuesta de refresh no exitosa:', response.data)
        return false
      }
    } catch (error) {
      console.error('Error al refrescar token:', error.response?.status, error.response?.data)
      
      if (error.response?.status === 401) {
        console.log('Refresh token expirado, limpiando sesión')
        this.logout()
        return false
      }
      
      this.logout()
      return false
    }
  }

  async getCurrentUser() {
    try {
      const response = await axios.get('/auth/me')
      
      if (response.data.success) {
        this.user = response.data.data || response.data
        localStorage.setItem('user', JSON.stringify(this.user))
        
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

const authService = new AuthService()

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

axios.interceptors.request.use(
  (config) => {
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

    if (error.response?.status === 401 && !originalRequest._retry && !originalRequest._skipAuthRefresh) {
      
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

      if (authService.isTokenExpired() || authService.shouldRefreshToken()) {
        try {
          const refreshed = await authService.refreshToken()
          
          if (refreshed) {
            refreshAttempts = 0
            
            processQueue(null, authService.token)
            
            originalRequest.headers.Authorization = `Bearer ${authService.token}`
            return axios(originalRequest)
          } else {
            processQueue(new Error('Token refresh failed'), null)
            
            authService.logout()
            if (window.location.pathname !== '/login') {
              window.location.href = '/login'
            }
          }
        } catch (refreshError) {
          processQueue(refreshError, null)
          
          authService.logout()
          if (window.location.pathname !== '/login') {
            window.location.href = '/login'
          }
        } finally {
          isRefreshing = false
        }
      } else {
        isRefreshing = false
        refreshAttempts = 0
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