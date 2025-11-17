/**
 * Favorites Store (Pinia)
 *
 * Store pour gérer les favoris utilisateur
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import favoriteService from '../services/favoriteService'

export const useFavoritesStore = defineStore('favorites', () => {
  // State
  const favorites = ref([])
  const favoriteIds = ref([])
  const loading = ref(false)
  const error = ref(null)
  const pagination = ref({
    total: 0,
    page: 1,
    limit: 20,
    pages: 0
  })

  // Getters
  const isFavorite = computed(() => {
    return (buildId) => favoriteIds.value.includes(buildId)
  })

  // Actions
  async function fetchAll(params = {}) {
    loading.value = true
    error.value = null
    try {
      const response = await favoriteService.getAll(params)
      favorites.value = response.data.favorites
      if (response.data.pagination) {
        pagination.value = response.data.pagination
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch favorites'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function add(buildId) {
    loading.value = true
    error.value = null
    try {
      const response = await favoriteService.add(buildId)
      favoriteIds.value.push(buildId)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to add favorite'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function remove(buildId) {
    loading.value = true
    error.value = null
    try {
      const response = await favoriteService.remove(buildId)
      favoriteIds.value = favoriteIds.value.filter(id => id !== buildId)
      favorites.value = favorites.value.filter(fav => fav.id !== buildId)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to remove favorite'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function toggle(buildId) {
    loading.value = true
    error.value = null
    try {
      const response = await favoriteService.toggle(buildId)

      if (response.data.action === 'added') {
        favoriteIds.value.push(buildId)
      } else {
        favoriteIds.value = favoriteIds.value.filter(id => id !== buildId)
        favorites.value = favorites.value.filter(fav => fav.id !== buildId)
      }

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to toggle favorite'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function check(buildId) {
    try {
      const response = await favoriteService.check(buildId)
      return response.data.is_favorite
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to check favorite'
      return false
    }
  }

  async function fetchFavoriteIds() {
    loading.value = true
    error.value = null
    try {
      const response = await favoriteService.getFavoriteIds()
      favoriteIds.value = response.data.favorite_build_ids
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch favorite IDs'
      throw err
    } finally {
      loading.value = false
    }
  }

  function clearError() {
    error.value = null
  }

  return {
    favorites,
    favoriteIds,
    loading,
    error,
    pagination,
    isFavorite,
    fetchAll,
    add,
    remove,
    toggle,
    check,
    fetchFavoriteIds,
    clearError
  }
})
