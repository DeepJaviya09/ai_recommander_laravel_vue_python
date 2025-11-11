<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'bg-gradient-to-r from-emerald-500 to-teal-400 text-white font-medium px-5 py-2 rounded-full',
      'hover:opacity-90 transition-all duration-300',
      'disabled:opacity-50 disabled:cursor-not-allowed',
      'flex items-center justify-center gap-2',
      sizeClasses
    ]"
    @click="$emit('click')"
  >
    <span v-if="loading" class="animate-spin">⏳</span>
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

const sizeClasses = computed(() => {
  const sizes = {
    sm: 'px-4 py-1.5 text-sm',
    md: 'px-5 py-2 text-base',
    lg: 'px-6 py-3 text-lg'
  }
  return sizes[props.size]
})
</script>

