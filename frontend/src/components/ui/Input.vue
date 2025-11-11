<template>
  <div class="w-full">
    <label v-if="label" class="block text-sm font-medium text-gray-800 mb-2">
      {{ label }}
    </label>
    <div class="relative">
      <div v-if="icon" class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <component :is="icon" class="h-5 w-5 text-gray-500" />
      </div>
      <input
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :required="required"
        :class="[
          'w-full bg-white border border-gray-300 rounded-lg px-4 py-3',
          'text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400',
          'transition-all duration-300',
          icon ? 'pl-10' : ''
        ]"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
    <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  icon: {
    type: Object,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  }
})

defineEmits(['update:modelValue'])
</script>

