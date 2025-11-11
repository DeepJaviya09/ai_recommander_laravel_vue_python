<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-4xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <Card>
          <div class="text-center">
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ stats.totalProducts || 0 }}</div>
            <div class="text-gray-500">Total Products</div>
          </div>
        </Card>
        <Card>
          <div class="text-center">
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ stats.totalUsers || 0 }}</div>
            <div class="text-gray-500">Total Users</div>
          </div>
        </Card>
        <Card>
          <div class="text-center">
            <div class="text-3xl font-bold text-gray-800 mb-2">{{ stats.totalCategories || 0 }}</div>
            <div class="text-gray-500">Categories</div>
          </div>
        </Card>
      </div>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card>
          <h2 class="text-2xl font-bold text-gray-800 mb-4">Quick Actions</h2>
          <div class="space-y-3">
            <router-link to="/admin/sync-model">
              <button class="w-full bg-gradient-to-r from-teal-500 to-emerald-400 text-white font-medium px-5 py-3 rounded-lg hover:opacity-90 transition">
                Sync AI Model
              </button>
            </router-link>
            <router-link to="/admin/products">
              <button class="w-full bg-gray-100 text-gray-800 font-medium px-5 py-3 rounded-lg hover:bg-gray-200 transition">
                Manage Products
              </button>
            </router-link>
          </div>
        </Card>

        <Card>
          <h2 class="text-2xl font-bold text-gray-800 mb-4">System Status</h2>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-gray-600">API Status</span>
              <span class="text-emerald-600">● Online</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-600">Database</span>
              <span class="text-emerald-600">● Connected</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-600">AI Model</span>
              <span class="text-teal-600">● Ready</span>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import Card from '@/components/ui/Card.vue'

const stats = ref({
  totalProducts: 0,
  totalUsers: 0,
  totalCategories: 0
})

const fetchStats = async () => {
  try {
    // Fetch products to count
    const productsResponse = await api.get('/products?per_page=1')
    stats.value.totalProducts = productsResponse.data.total || productsResponse.data.length || 0

    // Extract categories count from products
    const allProductsResponse = await api.get('/products')
    const allProducts = allProductsResponse.data.data || allProductsResponse.data || []
    const uniqueCategories = new Set()
    allProducts.forEach(product => {
      if (product.category) {
        uniqueCategories.add(product.category.id)
      }
    })
    stats.value.totalCategories = uniqueCategories.size
  } catch (error) {
    console.error('Failed to fetch stats', error)
  }
}

onMounted(() => {
  fetchStats()
})
</script>

