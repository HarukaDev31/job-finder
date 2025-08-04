const API_CONFIG = {
  baseURL: process.env.NODE_ENV === 'development' && window.location.port === '5173' 
    ? 'http://localhost:8000/api' 
    : '/api',
  
  auth: {
    login: '/auth/login',
    register: '/auth/register',
    logout: '/auth/logout',
    refresh: '/auth/refresh',
    me: '/auth/me'
  },
  
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  
  jwt: {
    expiresIn: 60,
    refreshBeforeExpiry: 5
  }
}

export { API_CONFIG };
export default API_CONFIG; 