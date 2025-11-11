<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Manage Products</h1>
        <Button @click="openCreateModal" size="lg">
          ➕ Create Product
        </Button>
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

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 6" :key="i" class="bg-white/70 backdrop-blur-md rounded-xl h-64 animate-pulse"></div>
      </div>

      <!-- Products Table -->
      <div v-else class="bg-white/70 backdrop-blur-md rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Image</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Name</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Category</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Price</th>
                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-800">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                  <img
                    :src="product.image_url || 'https://via.placeholder.com/100'"
                    :alt="product.name"
                    class="w-16 h-16 object-cover rounded-lg"
                  />
                </td>
                <td class="px-6 py-4">
                  <div class="text-gray-800 font-medium">{{ product.name }}</div>
                  <div class="text-gray-500 text-sm line-clamp-2">{{ product.description }}</div>
                </td>
                <td class="px-6 py-4">
                  <span class="text-emerald-600">{{ product.category?.name || 'N/A' }}</span>
                </td>
                <td class="px-6 py-4 text-gray-800 font-semibold">${{ product.price }}</td>
                <td class="px-6 py-4">
                  <div class="flex gap-2">
                    <button
                      @click="editProduct(product)"
                      class="px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm transition"
                    >
                      Edit
                    </button>
                    <button
                      @click="deleteProduct(product.id)"
                      class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm transition"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="products.length === 0" class="text-center py-12">
          <p class="text-gray-500 text-xl">No products found</p>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="px-6 py-4 flex justify-center gap-2">
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

      <!-- Create/Edit Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
        @click.self="closeModal"
      >
        <Card class="w-full max-w-2xl max-h-[90vh] overflow-y-auto">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">
              {{ editingProduct ? 'Edit Product' : 'Create Product' }}
            </h2>
            <button
              @click="closeModal"
              class="text-gray-500 hover:text-gray-800 text-2xl"
            >
              ×
            </button>
          </div>

          <form @submit.prevent="saveProduct" class="space-y-4">
            <Input
              v-model="form.name"
              label="Product Name"
              placeholder="Enter product name"
              required
              :error="errors.name"
            />
            <div>
              <label class="block text-sm font-medium text-gray-800 mb-2">Description</label>
              <textarea
                v-model="form.description"
                rows="4"
                class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                placeholder="Enter product description"
              ></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-800 mb-2">Category</label>
              <select
                v-model="form.category_id"
                required
                class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
              >
                <option value="">Select a category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
              <p v-if="errors.category_id" class="mt-1 text-sm text-red-600">{{ errors.category_id }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <Input
                v-model="form.price"
                type="number"
                step="0.01"
                label="Price"
                placeholder="0.00"
                required
                :error="errors.price"
              />
              <Input
                v-model="form.image_url"
                type="url"
                label="Image URL"
                placeholder="https://example.com/image.jpg"
                :error="errors.image_url"
              />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-800 mb-2">Tags (comma separated)</label>
              <input
                v-model="tagsInput"
                type="text"
                placeholder="tag1, tag2, tag3"
                class="w-full bg-white border border-gray-300 rounded-lg px-4 py-3 text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
              />
            </div>

            <div class="flex gap-4 pt-4">
              <Button type="submit" :loading="saving" class="flex-1">
                {{ editingProduct ? 'Update' : 'Create' }} Product
              </Button>
              <button
                type="button"
                @click="closeModal"
                class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full transition"
              >
                Cancel
              </button>
            </div>
          </form>
        </Card>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useToast } from 'vue-toastification'
import api from '@/services/api'
import Card from '@/components/ui/Card.vue'
import Input from '@/components/ui/Input.vue'
import Button from '@/components/ui/Button.vue'

const toast = useToast()

const products = ref([])
const categories = ref([])
const loading = ref(false)
const searchQuery = ref('')
const selectedCategory = ref('')
const pagination = ref(null)
const showModal = ref(false)
const editingProduct = ref(null)
const saving = ref(false)

const form = ref({
  name: '',
  description: '',
  category_id: '',
  price: '',
  image_url: '',
  tags: []
})

const tagsInput = ref('')
const errors = ref({})

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
    const params = { page }
    if (searchQuery.value) params.search = searchQuery.value
    if (selectedCategory.value) params.category_id = selectedCategory.value

    const response = await api.get('/admin/products', { params })
    products.value = response.data.data || response.data
    pagination.value = response.data
  } catch (error) {
    toast.error('Failed to fetch products')
    console.error('Failed to fetch products', error)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await api.get('/admin/categories')
    categories.value = response.data
  } catch (error) {
    console.error('Failed to fetch categories', error)
  }
}

const openCreateModal = () => {
  editingProduct.value = null
  form.value = {
    name: '',
    description: '',
    category_id: '',
    price: '',
    image_url: '',
    tags: []
  }
  tagsInput.value = ''
  errors.value = {}
  showModal.value = true
}

const editProduct = (product) => {
  editingProduct.value = product
  form.value = {
    name: product.name,
    description: product.description || '',
    category_id: product.category_id || product.category?.id || '',
    price: product.price,
    image_url: product.image_url || '',
    tags: product.tags || []
  }
  tagsInput.value = Array.isArray(product.tags) ? product.tags.join(', ') : ''
  errors.value = {}
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingProduct.value = null
  form.value = {
    name: '',
    description: '',
    category_id: '',
    price: '',
    image_url: '',
    tags: []
  }
  tagsInput.value = ''
  errors.value = {}
}

const saveProduct = async () => {
  errors.value = {}
  saving.value = true

  // Parse tags
  const tags = tagsInput.value
    .split(',')
    .map(tag => tag.trim())
    .filter(tag => tag.length > 0)

  const payload = {
    ...form.value,
    tags: tags.length > 0 ? tags : null,
    price: parseFloat(form.value.price)
  }

  try {
    if (editingProduct.value) {
      await api.put(`/admin/products/${editingProduct.value.id}`, payload)
      toast.success('Product updated successfully!')
    } else {
      await api.post('/admin/products', payload)
      toast.success('Product created successfully!')
    }
    closeModal()
    fetchProducts()
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      toast.error('Validation failed. Please check the form.')
    } else {
      toast.error(error.response?.data?.message || 'Failed to save product')
    }
  } finally {
    saving.value = false
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Are you sure you want to delete this product?')) {
    return
  }

  try {
    await api.delete(`/admin/products/${id}`)
    toast.success('Product deleted successfully!')
    fetchProducts()
  } catch (error) {
    toast.error('Failed to delete product')
  }
}

const changePage = (page) => {
  fetchProducts(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>

