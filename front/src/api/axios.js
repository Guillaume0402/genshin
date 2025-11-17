/**
 * Axios Configuration
 *
 * Configuration globale d'Axios pour les appels API
 * Gère les headers, le token JWT, et les intercepteurs
 */

import axios from 'axios'

// Configuration de base
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Intercepteur de requête : ajoute le token JWT si disponible
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Intercepteur de réponse : gestion des erreurs globales
api.interceptors.response.use(
  (response) => {
    return response
  },
  (error) => {
    // Gestion des erreurs 401 (non authentifié)
    if (error.response && error.response.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }

    // Gestion des erreurs 403 (accès refusé)
    if (error.response && error.response.status === 403) {
      console.error('Access forbidden:', error.response.data.message)
    }

    // Gestion des erreurs 500 (erreur serveur)
    if (error.response && error.response.status >= 500) {
      console.error('Server error:', error.response.data.message)
    }

    return Promise.reject(error)
  }
)

export default api
