<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
          <h1 class="text-4xl font-bold text-gray-800">Products</h1>
          <router-link v-if="userStore.isAdmin" to="/admin/products">
            <button class="bg-gradient-to-r from-emerald-500 to-teal-400 text-white font-medium px-5 py-2 rounded-full hover:opacity-90 transition">
              Manage Products
            </button>
          </router-link>
        </div>
        
        <!-- Search and Filter -->
        <div class="flex flex-col md:flex-row gap-4 mb-6">
          <div class="flex-1">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search products..."
              class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
              @input="debouncedSearch"
            />
          </div>
          <select
            v-model="selectedCategory"
            class="bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
            @change="fetchProducts"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="i in 8" :key="i" class="bg-white/10 backdrop-blur-lg rounded-2xl h-64 animate-pulse"></div>
      </div>

      <!-- Products Grid -->
      <div v-else-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in products"
          :key="product.id"
          :product="product"
          @click="goToProduct(product.id)"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-gray-500 text-xl">No products found</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="mt-8 flex justify-center gap-2">
        <button
          v-for="page in pagination.last_page"
          :key="page"
          @click="changePage(page)"
          :class="[
            'px-4 py-2 rounded-lg transition',
            page === pagination.current_page
              ? 'bg-emerald-500 text-white font-semibold'
              : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
          ]"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/store/userStore'
import api from '@/services/api'
import ProductCard from '@/components/ProductCard.vue'

const router = useRouter()
const userStore = useUserStore()

const products = ref([])
const categories = ref([])
const loading = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('')
const pagination = ref(null)

let searchTimeout = null

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchProducts()
  }, 500)
}

const fetchProducts = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page
    }
    if (searchQuery.value) {
      params.search = searchQuery.value
    }
    if (selectedCategory.value) {
      params.category_id = selectedCategory.value
    }

    const response = await api.get('/products', { params })
    products.value = response.data.data || response.data
    pagination.value = response.data
  } catch (error) {
    console.error('Failed to fetch products', error)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    // Fetch categories from products (extract unique categories)
    const response = await api.get('/products')
    const allProducts = response.data.data || response.data
    const uniqueCategories = new Map()
    allProducts.forEach(product => {
      if (product.category && !uniqueCategories.has(product.category.id)) {
        uniqueCategories.set(product.category.id, product.category)
      }
    })
    categories.value = Array.from(uniqueCategories.values())
  } catch (error) {
    console.error('Failed to fetch categories', error)
  }
}

const changePage = (page) => {
  fetchProducts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const goToProduct = (id) => {
  router.push(`/products/${id}`)
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>

