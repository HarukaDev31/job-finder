import Vue from 'vue'
import VueRouter from 'vue-router'
import { 
  Home, 
  Login, 
  Register, 
  Dashboard, 
  AdminDashboard, 
  Jobs, 
  AdminJobs, 
  Applications, 
  AdminApplications 
} from './components'
import authService from './services/auth.js'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/jobs',
    name: 'Jobs',
    component: Jobs,
    meta: { requiresAuth: true }
  },
  {
    path: '/applications',
    name: 'Applications',
    component: Applications,
    meta: { requiresAuth: true }
  },
  // Rutas de administrador
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/jobs',
    name: 'AdminJobs',
    component: AdminJobs,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/applications',
    name: 'AdminApplications',
    component: AdminApplications,
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

const router = new VueRouter({
  mode: 'history',
  base: '/',
  routes
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const isAuthenticated = authService.isAuthenticated()
  const user = authService.getUser()
  const isAdmin = authService.isAdmin()

  // Si la ruta requiere autenticación
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      next('/login')
      return
    }

    // Si la ruta requiere ser admin
    if (to.matched.some(record => record.meta.requiresAdmin)) {
      if (!isAdmin) {
        next('/dashboard')
        return
      }
    }
  }

  // Si el usuario está autenticado y trata de ir a login/register
  if (isAuthenticated && (to.name === 'Login' || to.name === 'Register')) {
    if (isAdmin) {
      next('/admin/dashboard')
    } else {
      next('/dashboard')
    }
    return
  }

  next()
})

export default router 