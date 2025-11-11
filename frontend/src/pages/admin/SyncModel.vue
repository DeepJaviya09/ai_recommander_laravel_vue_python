<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-4">Sync AI Model</h1>
        <p class="text-gray-500 text-lg">Update the recommendation model with latest data</p>
      </div>

      <Card class="text-center">
        <div class="py-12">
          <div class="mb-8">
            <div class="text-6xl mb-4">🤖</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">AI Recommendation Model</h2>
            <p class="text-gray-600 mb-8">
              Click the button below to synchronize the AI model with the latest user interactions,
              purchases, and product data. This will improve recommendation accuracy.
            </p>
          </div>

          <button
            @click="handleSync"
            :disabled="syncing"
            class="px-8 py-4 text-lg bg-gradient-to-r from-teal-500 to-emerald-400 text-white font-medium rounded-full hover:opacity-90 hover:shadow-lg hover:shadow-emerald-200 transition disabled:opacity-50"
          >
            {{ syncing ? 'Syncing...' : '🔄 Sync AI Model' }}
          </button>

          <div v-if="lastSync" class="mt-8 text-gray-500 text-sm">
            Last synced: {{ lastSync }}
          </div>
        </div>
      </Card>

      <!-- Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <Card>
          <h3 class="text-xl font-bold text-gray-800 mb-3">What happens during sync?</h3>
          <ul class="text-gray-600 space-y-2 text-sm">
            <li>• Analyzes user purchase history</li>
            <li>• Processes product views and interactions</li>
            <li>• Updates recommendation algorithms</li>
            <li>• Improves personalization accuracy</li>
          </ul>
        </Card>

        <Card>
          <h3 class="text-xl font-bold text-gray-800 mb-3">Best Practices</h3>
          <ul class="text-gray-600 space-y-2 text-sm">
            <li>• Sync after significant data changes</li>
            <li>• Run sync during low-traffic periods</li>
            <li>• Monitor sync status regularly</li>
            <li>• Review recommendation quality</li>
          </ul>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import Card from '@/components/ui/Card.vue'

const toast = useToast()

const syncing = ref(false)
const lastSync = ref(null)

const handleSync = async () => {
  syncing.value = true
  try {
    const response = await api.post('/admin/sync-model')
    toast.success('AI Model updated successfully! 🎉')
    lastSync.value = new Date().toLocaleString()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to sync model')
    console.error('Sync error', error)
  } finally {
    syncing.value = false
  }
}
</script>

