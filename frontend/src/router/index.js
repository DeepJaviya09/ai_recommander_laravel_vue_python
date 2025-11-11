import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/store/userStore'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      redirect: '/products'
    },
    {
      path: '/login',
      name: 'Login',
      component: () => import('@/pages/auth/Login.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/register',
      name: 'Register',
      component: () => import('@/pages/auth/Register.vue'),
      meta: { requiresGuest: true }
    },
    {
      path: '/products',
      name: 'Products',
      component: () => import('@/pages/user/Products.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/products/:id',
      name: 'ProductDetails',
      component: () => import('@/pages/user/ProductDetails.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/recommendations',
      name: 'Recommendations',
      component: () => import('@/pages/user/Recommendations.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/admin/dashboard',
      name: 'AdminDashboard',
      component: () => import('@/pages/admin/Dashboard.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/sync-model',
      name: 'SyncModel',
      component: () => import('@/pages/admin/SyncModel.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    },
    {
      path: '/admin/products',
      name: 'AdminProducts',
      component: () => import('@/pages/admin/Products.vue'),
      meta: { requiresAuth: true, requiresAdmin: true }
    }
  ]
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore()

  // If route requires auth, check if user is authenticated
  if (to.meta.requiresAuth) {
    if (!userStore.isAuthenticated) {
      next({ name: 'Login', query: { redirect: to.fullPath } })
      return
    }

    // If user data is not loaded, fetch it
    if (!userStore.user) {
      await userStore.fetchUser()
    }

    // If route requires admin, check if user is admin
    if (to.meta.requiresAdmin && !userStore.isAdmin) {
      next({ name: 'Products' })
      return
    }
  }

  // If route requires guest (login/register), redirect if already authenticated
  if (to.meta.requiresGuest && userStore.isAuthenticated) {
    next({ name: 'Products' })
    return
  }

  next()
})

export default router

