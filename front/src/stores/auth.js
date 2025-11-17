/**
 * Auth Store (Pinia)
 *
 * Store pour gérer l'authentification et l'état utilisateur
 */

import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import authService from '../services/authService'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(null)
  const loading = ref(false)
  const error = ref(null)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const currentUser = computed(() => user.value)

  // Actions
  async function register(userData) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.register(userData)
      user.value = response.data.user
      token.value = response.data.token
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Registration failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function login(credentials) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.login(credentials)
      user.value = response.data.user
      token.value = response.data.token
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Login failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    try {
      await authService.logout()
    } catch (err) {
      console.error('Logout error:', err)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      loading.value = false
    }
  }

  async function fetchUser() {
    loading.value = true
    error.value = null
    try {
      const response = await authService.me()
      user.value = response.data.user
      localStorage.setItem('user', JSON.stringify(user.value))
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to fetch user'
      logout()
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateProfile(profileData) {
    loading.value = true
    error.value = null
    try {
      const response = await authService.updateProfile(profileData)
      user.value = response.data.user
      localStorage.setItem('user', JSON.stringify(user.value))
      return response
    } catch (err) {
      error.value = err.response?.data?.message || 'Profile update failed'
      throw err
    } finally {
      loading.value = false
    }
  }

  function initializeAuth() {
    const storedToken = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')

    if (storedToken && storedUser) {
      token.value = storedToken
      user.value = JSON.parse(storedUser)
    }
  }

  return {
    user,
    token,
    loading,
    error,
    isAuthenticated,
    currentUser,
    register,
    login,
    logout,
    fetchUser,
    updateProfile,
    initializeAuth
  }
})
