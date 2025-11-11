<template>
  <nav class="bg-gradient-to-r from-slate-800 to-gray-700 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <router-link to="/products" class="text-2xl font-bold text-gray-100 hover:text-emerald-400 transition">
            🛍️ AI Shop
          </router-link>
        </div>
        
        <div class="flex items-center space-x-4">
          <template v-if="userStore.isAuthenticated">
            <router-link
              v-if="!userStore.isAdmin"
              to="/products"
              class="text-gray-100 hover:text-emerald-400 px-3 py-2 rounded-md text-sm font-medium transition"
            >
              Products
            </router-link>
            <router-link
              v-if="!userStore.isAdmin"
              to="/recommendations"
              class="text-gray-100 hover:text-emerald-400 px-3 py-2 rounded-md text-sm font-medium transition"
            >
              Recommendations
            </router-link>
            <router-link
              v-if="userStore.isAdmin"
              to="/admin/dashboard"
              class="text-gray-100 hover:text-emerald-400 px-3 py-2 rounded-md text-sm font-medium transition"
            >
              Dashboard
            </router-link>
            <router-link
              v-if="userStore.isAdmin"
              to="/admin/products"
              class="text-gray-100 hover:text-emerald-400 px-3 py-2 rounded-md text-sm font-medium transition"
            >
              Manage Products
            </router-link>
            <router-link
              v-if="userStore.isAdmin"
              to="/admin/sync-model"
              class="text-gray-100 hover:text-emerald-400 px-3 py-2 rounded-md text-sm font-medium transition"
            >
              Sync Model
            </router-link>
            <span class="text-gray-300 text-sm">{{ userStore.user?.name }}</span>
            <button
              @click="handleLogout"
              class="bg-white/10 hover:bg-white/20 text-gray-100 px-4 py-2 rounded-full text-sm font-medium transition"
            >
              Logout
            </button>
          </template>
          <template v-else>
            <router-link
              to="/login"
              class="text-gray-100 hover:text-emerald-400 px-3 py-2 rounded-md text-sm font-medium transition"
            >
              Login
            </router-link>
            <router-link
              to="/register"
              class="bg-white/10 hover:bg-white/20 text-gray-100 px-4 py-2 rounded-full text-sm font-medium transition"
            >
              Register
            </router-link>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useUserStore } from '@/store/userStore'
import { useToast } from 'vue-toastification'

const userStore = useUserStore()
const toast = useToast()

const handleLogout = async () => {
  await userStore.logout()
  toast.success('Logged out successfully')
}
</script>

