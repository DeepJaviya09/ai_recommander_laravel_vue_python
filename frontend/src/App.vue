<template>
  <div id="app" class="min-h-screen">
    <Navbar v-if="showNavbar" />
    <router-view v-slot="{ Component, route }">
      <transition
        name="fade"
        mode="out-in"
      >
        <component :is="Component" :key="route.path" />
      </transition>
    </router-view>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import Navbar from '@/components/Navbar.vue'

const route = useRoute()

const showNavbar = computed(() => {
  // Hide navbar on login/register pages
  return !['Login', 'Register'].includes(route.name)
})
</script>

<style>
#app {
  font-family: 'Inter', 'Poppins', system-ui, sans-serif;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>



