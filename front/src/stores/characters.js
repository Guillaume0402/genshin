/**
 * Characters Store (Pinia)
 *
 * Store pour gérer les personnages Genshin Impact
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'
import characterService from '../services/characterService'

export const useCharactersStore = defineStore('characters', () => {
  // State
  const characters = ref([])
  const currentCharacter = ref(null)
  const popularCharacters = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    total: 0,
    page: 1,
    limit: 20,
    pages: 0
  })

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getAll(params)
      characters.value = response.data.characters
      if (response.data.pagination) {
        pagination.value = response.data.pagination
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch characters'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchById(id) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getById(id)
      currentCharacter.value = response.data.character
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch character'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchCharacterBuilds(id) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getBuilds(id)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch character builds'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchByElement(element) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getByElement(element)
      characters.value = response.data.characters
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch characters by element'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchByWeapon(weaponType) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getByWeapon(weaponType)
      characters.value = response.data.characters
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch characters by weapon'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchByRarity(rarity) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getByRarity(rarity)
      characters.value = response.data.characters
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch characters by rarity'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchPopular(limit = 10) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.getPopular(limit)
      popularCharacters.value = response.data.characters
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch popular characters'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function search(query) {
    loading.value = true
    error.value = null
    try {
      const response = await characterService.search(query)
      characters.value = response.data.characters
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Search failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  function clearCurrentCharacter() {
    currentCharacter.value = null
  }

  function clearError() {
    error.value = null
  }

  return {
    characters,
    currentCharacter,
    popularCharacters,
    loading,
    error,
    pagination,
    fetchAll,
    fetchById,
    fetchCharacterBuilds,
    fetchByElement,
    fetchByWeapon,
    fetchByRarity,
    fetchPopular,
    search,
    clearCurrentCharacter,
    clearError
  }
})
