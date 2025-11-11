import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'
import router from '@/router'

export const useUserStore = defineStore('user', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user = ref(null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  // Load user from localStorage on init
  const savedUser = localStorage.getItem('user')
  if (savedUser) {
    try {
      user.value = JSON.parse(savedUser)
    } catch (e) {
      console.error('Failed to parse saved user', e)
    }
  }

  async function login(email, password) {
    try {
      const response = await api.post('/login', { email, password })
      token.value = response.data.token
      localStorage.setItem('token', token.value)
      
      // Fetch user data
      await fetchUser()
      
      return { success: true }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed'
      }
    }
  }

  async function register(name, email, password) {
    try {
      const response = await api.post('/register', { name, email, password })
      token.value = response.data.token
      localStorage.setItem('token', token.value)
      
      // Fetch user data
      await fetchUser()
      
      return { success: true }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Registration failed'
      }
    }
  }

  async function fetchUser() {
    try {
      const response = await api.get('/user')
      user.value = response.data
      localStorage.setItem('user', JSON.stringify(response.data))
    } catch (error) {
      console.error('Failed to fetch user', error)
      logout()
    }
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      router.push('/login')
    }
  }

  return {
    token,
    user,
    isAuthenticated,
    isAdmin,
    login,
    register,
    fetchUser,
    logout
  }
})



