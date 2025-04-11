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
          component: Home
        },
        {
          path: 'lar',
          name: 'lar',
          component: () => import('../views/Lar.vue')
        },
        {
          path: 'sobre',
          name: 'sobre',
          component: () => import('../views/Sobre.vue')
        },
        {
          path: 'comprar',
          name: 'comprar',
          component: () => import('../views/Comprar.vue')
        },
        {
          path: 'contato',
          name: 'contato',
          component: () => import('../views/Contato.vue')
        },
        {
          path: 'carrinho',
          name: 'carrinho',
          component: () => import('../views/Carrinho.vue')
        },
        {
          path: 'conta',
          name: 'conta',
          component: () => import('../views/Conta.vue')
        },
        {
          path: 'produto/:id',
          name: 'produto-detalhe',
          component: () => import('../views/ProdutoDetalhe.vue')
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
      path: '/dashboard',
      component: DashboardLayout,
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard-home',
          component: () => import('../views/dashboard/Home.vue')
        },
        {
          path: 'products',
          name: 'dashboard-products',
          component: () => import('../views/dashboard/Products.vue')
        },
        {
          path: 'categories',
          name: 'dashboard-categories',
          component: () => import('../views/dashboard/Categories.vue')
        },
        {
          path: 'orders',
          name: 'dashboard-orders',
          component: () => import('../views/dashboard/Orders.vue')
        },
        {
          path: 'users',
          name: 'dashboard-users',
          component: () => import('../views/dashboard/Users.vue')
        },
        {
          path: 'settings',
          name: 'dashboard-settings',
          component: () => import('../views/dashboard/Settings.vue')
        }
      ]
    },
    {
      path: '/produtos',
      name: 'products',
      component: () => import('../views/dashboard/Products.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/categorias',
      name: 'categories',
      component: () => import('../views/dashboard/Categories.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/pedidos',
      name: 'orders',
      component: () => import('../views/dashboard/Orders.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/usuarios',
      name: 'users',
      component: () => import('../views/dashboard/Users.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/dashboard'
    }
  ]
})

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = await authStore.checkAuth()

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
