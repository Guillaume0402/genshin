/**
 * Character Service
 *
 * Service pour gérer les personnages Genshin Impact
 */

import api from '../api/axios'

export default {
  /**
   * Récupère tous les personnages avec pagination et filtres
   * @param {Object} params - Paramètres de recherche (page, limit, element, weapon_type, rarity, region, search)
   * @returns {Promise}
   */
  async getAll(params = {}) {
    const response = await api.get('/characters', { params })
    return response.data
  },

  /**
   * Récupère un personnage par son ID
   * @param {Number} id - ID du personnage
   * @returns {Promise}
   */
  async getById(id) {
    const response = await api.get(`/characters/${id}`)
    return response.data
  },

  /**
   * Récupère les builds d'un personnage
   * @param {Number} id - ID du personnage
   * @returns {Promise}
   */
  async getBuilds(id) {
    const response = await api.get(`/characters/${id}/builds`)
    return response.data
  },

  /**
   * Récupère les personnages par élément
   * @param {String} element - Élément (Pyro, Hydro, etc.)
   * @returns {Promise}
   */
  async getByElement(element) {
    const response = await api.get(`/characters/element/${element}`)
    return response.data
  },

  /**
   * Récupère les personnages par type d'arme
   * @param {String} weaponType - Type d'arme
   * @returns {Promise}
   */
  async getByWeapon(weaponType) {
    const response = await api.get(`/characters/weapon/${weaponType}`)
    return response.data
  },

  /**
   * Récupère les personnages par rareté
   * @param {Number} rarity - Rareté (4 ou 5)
   * @returns {Promise}
   */
  async getByRarity(rarity) {
    const response = await api.get(`/characters/rarity/${rarity}`)
    return response.data
  },

  /**
   * Récupère les personnages les plus populaires
   * @param {Number} limit - Nombre de résultats
   * @returns {Promise}
   */
  async getPopular(limit = 10) {
    const response = await api.get('/characters/popular', { params: { limit } })
    return response.data
  },

  /**
   * Recherche de personnages
   * @param {String} query - Terme de recherche
   * @returns {Promise}
   */
  async search(query) {
    const response = await api.get('/characters/search', { params: { q: query } })
    return response.data
  }
}
