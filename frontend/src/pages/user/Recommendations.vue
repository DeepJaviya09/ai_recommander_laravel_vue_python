<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">✨ Personalized Recommendations</h1>
        <p class="text-gray-500 text-lg">Products tailored just for you</p>
        <p v-if="recommendationScore" class="text-sm text-gray-400 mt-2">
          AI Match Score: {{ recommendationScore }}%
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div v-for="i in 8" :key="i" class="bg-white/70 backdrop-blur-md rounded-xl h-64 animate-pulse"></div>
      </div>

      <!-- Recommendations Grid -->
      <div v-else-if="recommendations.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in recommendations"
          :key="product.id"
          :product="product"
          @click="goToProduct(product.id)"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <div class="bg-white/70 backdrop-blur-md rounded-xl shadow-md p-12 max-w-md mx-auto">
          <p class="text-gray-800 text-xl mb-4">No recommendations yet</p>
          <p class="text-gray-500">Start browsing and purchasing products to get personalized recommendations!</p>
          <router-link to="/products">
            <Button class="mt-6">Browse Products</Button>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import { useUserStore } from '@/store/userStore'
import ProductCard from '@/components/ProductCard.vue'
import Button from '@/components/ui/Button.vue'

const router = useRouter()
const toast = useToast()
const userStore = useUserStore()

const recommendations = ref([])
const loading = ref(false)
const recommendationScore = ref(null)

const fetchRecommendations = async () => {
  loading.value = true
  try {
    // Get current user ID
    const userId = userStore.user?.id
    
    if (!userId) {
      toast.error('Please log in to see recommendations')
      router.push('/login')
      return
    }

    // Call the new AI-powered recommendation endpoint
    const response = await api.get(`/recommend/user/${userId}`, {
      params: { limit: 12 } // Request 12 recommendations
    })

    // ✅ Handle AI recommendation response format:
    // { user_id, source: "user-profile", recommendations: [{ id, score, payload }, ...] }
    if (response.data && Array.isArray(response.data.recommendations)) {
      // Extract payload from each recommendation (payload contains full product data)
      recommendations.value = response.data.recommendations.map(rec => ({
        ...rec.payload, // Spread the product data from payload
        recommendation_score: rec.score // Optionally include the score
      }))
      
      // Show average score if available
      if (recommendations.value.length > 0) {
        const avgScore = response.data.recommendations.reduce((sum, r) => sum + r.score, 0) / response.data.recommendations.length
        recommendationScore.value = (avgScore * 100).toFixed(1)
      }
    } else if (response.data?.message) {
      // Handle case where user has no activity
      recommendations.value = []
      toast.info(response.data.message)
    } else {
      recommendations.value = []
    }

  } catch (error) {
    console.error('Failed to fetch recommendations', error)
    
    // Handle specific error cases
    if (error.response?.status === 401) {
      toast.error('Please log in to see recommendations')
      router.push('/login')
    } else if (error.response?.status === 503) {
      toast.error('AI recommendation service is temporarily unavailable')
    } else if (error.response?.status !== 404) {
      toast.error('Failed to load recommendations')
    }
    
    recommendations.value = []
  } finally {
    loading.value = false
  }
}

const goToProduct = (id) => {
  router.push(`/products/${id}`)
}

onMounted(() => {
  fetchRecommendations()
})
</script>
