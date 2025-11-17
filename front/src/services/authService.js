/**
 * Authentication Service
 *
 * Service pour gérer l'authentification des utilisateurs
 */

import api from '../api/axios'

export default {
  /**
   * Inscription d'un nouvel utilisateur
   * @param {Object} userData - Données de l'utilisateur (username, email, password)
   * @returns {Promise}
   */
  async register(userData) {
    const response = await api.post('/auth/register', userData)
    return response.data
  },

  /**
   * Connexion d'un utilisateur
   * @param {Object} credentials - Identifiants (email, password)
   * @returns {Promise}
   */
  async login(credentials) {
    const response = await api.post('/auth/login', credentials)
    return response.data
  },

  /**
   * Récupère les informations de l'utilisateur connecté
   * @returns {Promise}
   */
  async me() {
    const response = await api.get('/auth/me')
    return response.data
  },

  /**
   * Mise à jour du profil utilisateur
   * @param {Object} profileData - Nouvelles données du profil
   * @returns {Promise}
   */
  async updateProfile(profileData) {
    const response = await api.put('/auth/profile', profileData)
    return response.data
  },

  /**
   * Déconnexion de l'utilisateur
   * @returns {Promise}
   */
  async logout() {
    const response = await api.post('/auth/logout')
    return response.data
  }
}
