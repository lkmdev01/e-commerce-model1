import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/lar',
      name: 'lar',
      component: () => import('../views/Lar.vue')
    },
    {
      path: '/sobre',
      name: 'sobre',
      component: () => import('../views/Sobre.vue')
    },
    {
      path: '/comprar',
      name: 'comprar',
      component: () => import('../views/Comprar.vue')
    },
    {
      path: '/contato',
      name: 'contato',
      component: () => import('../views/Contato.vue')
    },
    {
      path: '/carrinho',
      name: 'carrinho',
      component: () => import('../views/Carrinho.vue')
    },
    {
      path: '/conta',
      name: 'conta',
      component: () => import('../views/Conta.vue')
    },
    {
      path: '/produto/:id',
      name: 'produto-detalhe',
      component: () => import('../views/ProdutoDetalhe.vue')
    }
  ]
})

export default router
