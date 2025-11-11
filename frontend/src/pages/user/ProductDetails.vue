<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <button
        @click="$router.back()"
        class="mb-6 text-gray-600 hover:text-gray-800 flex items-center gap-2 transition"
      >
        ← Back to Products
      </button>

      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-800 text-xl">Loading...</p>
      </div>

      <div v-else-if="product" class="bg-white/70 backdrop-blur-md rounded-xl shadow-md overflow-hidden">
        <div class="grid md:grid-cols-2 gap-8 p-8">
          <!-- Product Image -->
          <div class="relative h-96 rounded-xl overflow-hidden bg-gray-100">
            <img
              :src="product.image_url || 'https://via.placeholder.com/600x400'"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Product Info -->
          <div class="flex flex-col justify-between">
            <div>
              <div class="mb-4">
                <span v-if="product.category" class="text-sm text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                  {{ product.category.name }}
                </span>
              </div>
              <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ product.name }}</h1>
              <p class="text-2xl font-bold text-emerald-600 mb-6">${{ product.price }}</p>
              <p class="text-gray-600 mb-6 leading-relaxed">{{ product.description }}</p>

              <!-- Tags -->
              <div v-if="product.tags && normalizeTags(product.tags).length > 0" class="flex flex-wrap gap-2 mb-6">
                <span
                  v-for="tag in normalizeTags(product.tags)"
                  :key="tag"
                  class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full"
                >
                  {{ tag }}
                </span>
              </div>
            </div>

            <!-- Actions -->
            <div class="space-y-4">
              <Button
                v-if="!isAdmin"
                type="button"
                @click="handleBuy"
                :loading="buying"
                class="w-full"
                size="lg"
              >
                🛒 Buy Now
              </Button>
              <div v-else class="text-center">
                <p class="text-gray-500 italic">Admin users cannot purchase products</p>
                <router-link to="/admin/products">
                  <Button class="mt-4 w-full" size="lg">
                    Manage Products
                  </Button>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from 'vue-toastification'
import { useUserStore } from '@/store/userStore'
import api from '@/services/api'
import Button from '@/components/ui/Button.vue'

const route = useRoute()
const toast = useToast()
const userStore = useUserStore()

const product = ref(null)
const loading = ref(true)
const buying = ref(false)

const isAdmin = computed(() => userStore.isAdmin)

const fetchProduct = async () => {
  loading.value = true
  try {
    const response = await api.get(`/products/${route.params.id}`)
    product.value = response.data

    // Log product visit only for non-admin users
    if (!userStore.isAdmin) {
      try {
        await api.post(`/product/${route.params.id}/visit`)
      } catch (error) {
        console.error('Failed to log visit', error)
      }
    }
  } catch (error) {
    toast.error('Failed to load product')
    console.error('Failed to fetch product', error)
  } finally {
    loading.value = false
  }
}

const handleBuy = async () => {
  if (buying.value) return // Prevent double click
  buying.value = true

  try {
    const response = await api.post(`/product/${route.params.id}/buy`)
    toast.success('Purchase successful! 🎉')
  } catch (error) {
    console.error(error)
    toast.error(error.response?.data?.message || 'Purchase failed')
  } finally {
    buying.value = false
  }
}

const normalizeTags = (tags) => {
  if (!tags) return []

  if (Array.isArray(tags)) return tags

  if (typeof tags === 'string') {
    try {
      // Try to fix malformed JSON
      const fixed = tags.replace(/'/g, '"')
      const parsed = JSON.parse(fixed)
      if (Array.isArray(parsed)) return parsed
    } catch (e) {
      // fallback: clean manual splitting
      return tags
        .replace(/[\[\]'"]/g, '')
        .split(',')
        .map(t => t.trim())
        .filter(Boolean)
    }
  }

  return []
}


onMounted(() => {
  console.log('Mounted once');
  fetchProduct()
})
</script>

