/**
 * Favorite Service
 *
 * Service pour gérer les favoris
 */

import api from '../api/axios'

export default {
  /**
   * Récupère tous les favoris de l'utilisateur
   * @param {Object} params - Paramètres de pagination
   * @returns {Promise}
   */
  async getAll(params = {}) {
    const response = await api.get('/favorites', { params })
    return response.data
  },

  /**
   * Ajoute un build aux favoris
   * @param {Number} buildId - ID du build
   * @returns {Promise}
   */
  async add(buildId) {
    const response = await api.post('/favorites', { build_id: buildId })
    return response.data
  },

  /**
   * Retire un build des favoris
   * @param {Number} buildId - ID du build
   * @returns {Promise}
   */
  async remove(buildId) {
    const response = await api.delete(`/favorites/${buildId}`)
    return response.data
  },

  /**
   * Toggle le statut favori d'un build
   * @param {Number} buildId - ID du build
   * @returns {Promise}
   */
  async toggle(buildId) {
    const response = await api.post('/favorites/toggle', { build_id: buildId })
    return response.data
  },

  /**
   * Vérifie si un build est en favori
   * @param {Number} buildId - ID du build
   * @returns {Promise}
   */
  async check(buildId) {
    const response = await api.get(`/favorites/check/${buildId}`)
    return response.data
  },

  /**
   * Récupère les IDs de tous les builds favoris
   * @returns {Promise}
   */
  async getFavoriteIds() {
    const response = await api.get('/favorites/ids')
    return response.data
  }
}
