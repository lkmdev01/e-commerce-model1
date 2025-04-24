import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import DefaultLayout from '../layouts/DefaultLayout.vue'
import DashboardLayout from '../layouts/DashboardLayout.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: DefaultLayout,
      children: [
        {
          path: '',
          name: 'home',
          component: Home,
          meta: { public: true }
        },
        {
          path: 'lar',
          name: 'lar',
          component: () => import('../views/Lar.vue'),
          meta: { public: true }
        },
        {
          path: 'sobre',
          name: 'sobre',
          component: () => import('../views/Sobre.vue'),
          meta: { public: true }
        },
        {
          path: 'comprar',
          name: 'comprar',
          component: () => import('../views/Comprar.vue'),
          meta: { public: true }
        },
        {
          path: 'contato',
          name: 'contato',
          component: () => import('../views/Contato.vue'),
          meta: { public: true }
        },
        {
          path: 'carrinho',
          name: 'carrinho',
          component: () => import('../views/Carrinho.vue'),
          meta: { public: true }
        },
        {
          path: 'produto/:id',
          name: 'produto-detalhe',
          component: () => import('../views/ProdutoDetalhe.vue'),
          meta: { public: true }
        }
      ]
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/auth/Login.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/auth/Register.vue'),
      meta: { guest: true }
    },
    {
      path: '/forgot-password',
      name: 'forgot-password',
      component: () => import('@/views/auth/ForgotPassword.vue'),
      meta: { guest: true }
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: () => import('@/views/auth/ResetPassword.vue'),
      meta: { guest: true }
    },
    {
      path: '/dashboard',
      component: DashboardLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('../views/dashboard/Home.vue'),
          meta: { requiresAdmin: true }
        },
        {
          path: 'products',
          name: 'dashboard-products',
          component: () => import('../views/dashboard/Products.vue'),
          meta: { requiresAdmin: true }
        },
        {
          path: 'categories',
          name: 'dashboard-categories',
          component: () => import('../views/dashboard/Categories.vue'),
          meta: { requiresAdmin: true }
        },
        {
          path: 'orders',
          name: 'dashboard-orders',
          component: () => import('../views/dashboard/Orders.vue'),
          meta: { requiresAuth: true }
        },
        {
          path: 'users',
          name: 'dashboard-users',
          component: () => import('../views/dashboard/Users.vue'),
          meta: { requiresAdmin: true }
        },
        {
          path: 'settings',
          name: 'dashboard-settings',
          component: () => import('../views/dashboard/Settings.vue'),
          meta: { requiresAdmin: true }
        },
        {
          path: 'profile',
          name: 'dashboard-profile',
          component: () => import('../views/dashboard/UserDashboard.vue'),
          meta: { requiresAuth: true }
        },
        {
          path: 'auth-check',
          name: 'auth-check',
          component: () => import('../views/dashboard/auth-check.vue'),
          meta: { requiresAuth: true }
        }
      ]
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/'
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  let isAuthenticated = false
  let isAdmin = false

  // Se a rota é pública, permite o acesso imediatamente
  if (to.meta.public) {
    next()
    return
  }

  // Verificar autenticação apenas se necessário
  if (to.meta.requiresAuth || to.meta.requiresAdmin) {
    isAuthenticated = await authStore.checkAuth()
    isAdmin = authStore.user?.role === 'admin'
  }

  // Se a rota requer autenticação e o usuário não está autenticado
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  }
  // Se a rota requer admin e o usuário não é admin
  else if (to.meta.requiresAdmin && !isAdmin) {
    next('/dashboard/profile')
  }
  // Se a rota é para visitantes e o usuário está autenticado
  else if (to.meta.guest && isAuthenticated) {
    if (isAdmin) {
      next('/dashboard')
    } else {
      next('/dashboard/profile')
    }
  }
  // Se tudo estiver ok, permite o acesso
  else {
    next()
  }
})

export default router
