<template>
  <div
    class="bg-white/70 backdrop-blur-md rounded-xl shadow-md overflow-hidden cursor-pointer group hover:scale-105 hover:shadow-lg hover:shadow-emerald-100 transition-all duration-300 transform hover:rotate-1"
    @click="$emit('click')"
  >
    <div class="relative h-48 overflow-hidden bg-gray-100">
      <img
        :src="product.image_url || 'https://via.placeholder.com/400x300'"
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    </div>
    <div class="p-4">
      <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ product.name }}</h3>
      <p v-if="product.category" class="text-xs text-emerald-600 mb-2">{{ product.category.name }}</p>
      <div class="flex items-center justify-between">
        <span class="text-2xl font-bold text-emerald-600">${{ product.price }}</span>
        <button
          class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-400 text-white text-sm rounded-full hover:opacity-90 transition-all"
          @click.stop="$emit('buy')"
        >
          View
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true
  }
})

defineEmits(['click', 'buy'])
</script>

