/**
 * Builds Store (Pinia)
 *
 * Store pour gérer les builds de personnages
 */

import { defineStore } from 'pinia'
import { ref } from 'vue'
import buildService from '../services/buildService'

export const useBuildsStore = defineStore('builds', () => {
  // State
  const builds = ref([])
  const currentBuild = ref(null)
  const myBuilds = ref([])
  const topRatedBuilds = ref([])
  const recentBuilds = ref([])
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
      const response = await buildService.getAll(params)
      builds.value = response.data.builds
      if (response.data.pagination) {
        pagination.value = response.data.pagination
      }
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch builds'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchById(id) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.getById(id)
      currentBuild.value = response.data.build
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch build'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function create(buildData) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.create(buildData)
      currentBuild.value = response.data.build
      myBuilds.value.unshift(response.data.build)
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to create build'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function update(id, buildData) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.update(id, buildData)
      currentBuild.value = response.data.build

      // Mise à jour dans myBuilds
      const index = myBuilds.value.findIndex(b => b.id === id)
      if (index !== -1) {
        myBuilds.value[index] = response.data.build
      }

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update build'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function deleteBuild(id) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.delete(id)

      // Suppression de myBuilds
      myBuilds.value = myBuilds.value.filter(b => b.id !== id)

      // Suppression de builds
      builds.value = builds.value.filter(b => b.id !== id)

      if (currentBuild.value?.id === id) {
        currentBuild.value = null
      }

      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete build'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchMyBuilds() {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.getMyBuilds()
      myBuilds.value = response.data.builds
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch my builds'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchTopRated(limit = 10) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.getTopRated(limit)
      topRatedBuilds.value = response.data.builds
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch top builds'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function fetchRecent(limit = 10) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.getRecent(limit)
      recentBuilds.value = response.data.builds
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch recent builds'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function search(query, limit = 20) {
    loading.value = true
    error.value = null
    try {
      const response = await buildService.search(query, limit)
      builds.value = response.data.builds
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Search failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  function clearCurrentBuild() {
    currentBuild.value = null
  }

  function clearError() {
    error.value = null
  }

  return {
    builds,
    currentBuild,
    myBuilds,
    topRatedBuilds,
    recentBuilds,
    loading,
    error,
    pagination,
    fetchAll,
    fetchById,
    create,
    update,
    deleteBuild,
    fetchMyBuilds,
    fetchTopRated,
    fetchRecent,
    search,
    clearCurrentBuild,
    clearError
  }
})
