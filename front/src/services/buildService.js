/**
 * Build Service
 *
 * Service pour gérer les builds de personnages
 */

import api from '../api/axios'

export default {
  /**
   * Récupère tous les builds avec pagination et filtres
   * @param {Object} params - Paramètres de recherche (page, limit, character_id, element, search, sort)
   * @returns {Promise}
   */
  async getAll(params = {}) {
    const response = await api.get('/builds', { params })
    return response.data
  },

  /**
   * Récupère un build par son ID
   * @param {Number} id - ID du build
   * @returns {Promise}
   */
  async getById(id) {
    const response = await api.get(`/builds/${id}`)
    return response.data
  },

  /**
   * Crée un nouveau build
   * @param {Object} buildData - Données du build
   * @returns {Promise}
   */
  async create(buildData) {
    const response = await api.post('/builds', buildData)
    return response.data
  },

  /**
   * Met à jour un build existant
   * @param {Number} id - ID du build
   * @param {Object} buildData - Nouvelles données du build
   * @returns {Promise}
   */
  async update(id, buildData) {
    const response = await api.put(`/builds/${id}`, buildData)
    return response.data
  },

  /**
   * Supprime un build
   * @param {Number} id - ID du build
   * @returns {Promise}
   */
  async delete(id) {
    const response = await api.delete(`/builds/${id}`)
    return response.data
  },

  /**
   * Récupère les builds de l'utilisateur connecté
   * @returns {Promise}
   */
  async getMyBuilds() {
    const response = await api.get('/builds/my-builds')
    return response.data
  },

  /**
   * Récupère les builds les mieux notés
   * @param {Number} limit - Nombre de résultats
   * @returns {Promise}
   */
  async getTopRated(limit = 10) {
    const response = await api.get('/builds/top-rated', { params: { limit } })
    return response.data
  },

  /**
   * Récupère les builds les plus récents
   * @param {Number} limit - Nombre de résultats
   * @returns {Promise}
   */
  async getRecent(limit = 10) {
    const response = await api.get('/builds/recent', { params: { limit } })
    return response.data
  },

  /**
   * Recherche de builds
   * @param {String} query - Terme de recherche
   * @param {Number} limit - Nombre de résultats
   * @returns {Promise}
   */
  async search(query, limit = 20) {
    const response = await api.get('/builds/search', { params: { q: query, limit } })
    return response.data
  }
}
