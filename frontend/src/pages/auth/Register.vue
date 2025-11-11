<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-200 via-gray-100 to-white flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white/80 backdrop-blur-md shadow-lg rounded-2xl p-6">
      <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h1>
        <p class="text-gray-500">Sign up to get started</p>
      </div>

      <form @submit.prevent="handleRegister" class="space-y-4">
        <Input
          v-model="form.name"
          type="text"
          label="Name"
          placeholder="Enter your name"
          required
          :error="errors.name"
        />
        <Input
          v-model="form.email"
          type="email"
          label="Email"
          placeholder="Enter your email"
          required
          :error="errors.email"
        />
        <Input
          v-model="form.password"
          type="password"
          label="Password"
          placeholder="Enter your password"
          required
          :error="errors.password"
        />

        <Button
          type="submit"
          :loading="loading"
          class="w-full"
        >
          Register
        </Button>
      </form>

      <p class="mt-4 text-center text-gray-500">
        Already have an account?
        <router-link to="/login" class="text-emerald-600 hover:text-emerald-700 font-semibold">
          Login here
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/store/userStore'
import { useToast } from 'vue-toastification'
import Card from '@/components/ui/Card.vue'
import Input from '@/components/ui/Input.vue'
import Button from '@/components/ui/Button.vue'

const router = useRouter()
const userStore = useUserStore()
const toast = useToast()

const form = ref({
  name: '',
  email: '',
  password: ''
})

const errors = ref({})
const loading = ref(false)

const handleRegister = async () => {
  errors.value = {}
  loading.value = true

  const result = await userStore.register(form.value.name, form.value.email, form.value.password)

  if (result.success) {
    toast.success('Registration successful!')
    router.push('/products')
  } else {
    console.log("err", result.message);
    
    toast.error(result.message || 'Registration failed')
    errors.value = { general: result.message }
  }

  loading.value = false
}
</script>

