import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'
import { useApi } from '@/composables/useApi'
import { useAuthStore } from './auth'

interface CartItem {
  id: number | string
  name: string
  price: string | number
  image: string
  quantity: number
}

interface ShippingOption {
  service: string
  price: number
  days: string
  code: string
}

interface ShippingAddress {
  cep: string
  logradouro: string
  bairro: string
  localidade: string
  uf: string
}

export const useCartStore = defineStore('cart', () => {
  const api = useApi()
  const authStore = useAuthStore()
  const items = ref<CartItem[]>([])
  const selectedShipping = ref<ShippingOption | null>(null)
  const shippingAddress = ref<ShippingAddress | null>(null)
  const isLoading = ref(false)

  // Carrega o carrinho do localStorage quando o store é inicializado
  const loadFromLocalStorage = () => {
    try {
      const savedCart = localStorage.getItem('cart')
      if (savedCart) {
        const cartData = JSON.parse(savedCart)
        items.value = cartData.items || []
        selectedShipping.value = cartData.selectedShipping || null
        shippingAddress.value = cartData.shippingAddress || null
      }
    } catch (error) {
      console.error('Erro ao carregar carrinho do localStorage:', error)
    }
  }

  // Salva o carrinho no localStorage
  const saveToLocalStorage = () => {
    try {
      const cartData = {
        items: items.value,
        selectedShipping: selectedShipping.value,
        shippingAddress: shippingAddress.value
      }
      localStorage.setItem('cart', JSON.stringify(cartData))
    } catch (error) {
      console.error('Erro ao salvar carrinho no localStorage:', error)
    }
  }

  // Salva o carrinho no backend (apenas para usuários autenticados)
  const saveToBackend = async () => {
    if (!authStore.isAuthenticated) return

    try {
      isLoading.value = true
      await api.post('/cart/save', {
        items: items.value,
        selectedShipping: selectedShipping.value,
        shippingAddress: shippingAddress.value
      })
    } catch (error) {
      console.error('Erro ao salvar carrinho no backend:', error)
    } finally {
      isLoading.value = false
    }
  }

  // Carrega o carrinho do backend (apenas para usuários autenticados)
  const loadFromBackend = async () => {
    if (!authStore.isAuthenticated) return

    try {
      isLoading.value = true
      const response = await api.get('/cart/load')
      const backendCart = response.data

      // Se tiver itens no carrinho local e no backend, mesclamos dando prioridade ao backend
      if (items.value.length > 0 && backendCart.items?.length > 0) {
        // Mescla os carrinhos, evitando duplicatas
        const mergedItems: CartItem[] = []
        const seenIds = new Set<string | number>()

        // Adiciona itens do backend primeiro
        backendCart.items.forEach((backendItem: CartItem) => {
          mergedItems.push({ ...backendItem })
          seenIds.add(backendItem.id)
        })

        // Adiciona itens do localStorage que não estão no backend
        items.value.forEach(localItem => {
          if (!seenIds.has(localItem.id)) {
            mergedItems.push({ ...localItem })
            seenIds.add(localItem.id)
          }
        })

        items.value = mergedItems
      } else if (backendCart.items?.length > 0) {
        // Se não tiver itens locais, apenas usa os do backend
        items.value = backendCart.items
      }

      // Usa o frete e endereço do backend se disponíveis
      if (backendCart.selectedShipping) {
        selectedShipping.value = backendCart.selectedShipping
      }

      if (backendCart.shippingAddress) {
        shippingAddress.value = backendCart.shippingAddress
      }

      // Salva a versão mesclada no localStorage
      saveToLocalStorage()

      // Não sincronizamos de volta para o backend aqui para evitar duplicações
      // O backend já contém os dados mais atualizados, exceto por itens que estavam apenas no localStorage
      // Esses itens serão salvos na próxima operação do carrinho
    } catch (error) {
      console.error('Erro ao carregar carrinho do backend:', error)
    } finally {
      isLoading.value = false
    }
  }

  // Inicializa o carrinho
  const initCart = async () => {
    // Primeiro carrega do localStorage
    loadFromLocalStorage()

    // Rastreamos se o carrinho estava vazio antes de sincronizar
    const wasEmpty = items.value.length === 0

    // Se o usuário estiver autenticado, carrega do backend
    if (authStore.isAuthenticated) {
      await loadFromBackend()

      // Se tínhamos itens locais que não estavam no backend e o carrinho não estava vazio,
      // salvamos após a mesclagem para garantir que tudo esteja sincronizado
      if (!wasEmpty && items.value.length > 0) {
        await saveToBackend()
      }
    }
  }

  // Sincroniza o carrinho quando o status de autenticação muda
  const syncCartWithAuth = async () => {
    if (authStore.isAuthenticated) {
      // Se o usuário acabou de fazer login, carregamos o carrinho do backend
      // e mesclamos com o carrinho local
      await loadFromBackend()

      // Após mesclar, verificamos se há itens locais que não estão no backend
      // e os salvamos. Isto é necessário apenas se o usuário tinha itens no
      // carrinho antes de fazer login.
      if (items.value.length > 0) {
        await saveToBackend()
      }
    } else {
      // Quando o usuário deslogar, mantém o carrinho apenas local
      saveToLocalStorage()
    }
  }

  // Propriedades computadas existentes
  const totalItems = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const totalAmount = computed(() => {
    return items.value.reduce((total, item) => {
      const price = typeof item.price === 'string' ? parseFloat(item.price) : item.price
      return total + price * item.quantity
    }, 0)
  })

  const cartTotal = computed(() => {
    return totalAmount.value + (selectedShipping.value ? selectedShipping.value.price : 0)
  })

  // Métodos existentes modificados para salvar alterações
  function addItem(product: { id: number | string; name: string; price: string | number; image: string }, quantity = 1) {
    const existingItem = items.value.find(item => item.id === product.id)

    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      items.value.push({
        ...product,
        quantity
      })
    }

    saveToLocalStorage()
    if (authStore.isAuthenticated) {
      saveToBackend()
    }
  }

  function removeItem(productId: number | string) {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)

      saveToLocalStorage()
      if (authStore.isAuthenticated) {
        saveToBackend()
      }
    }
  }

  function updateQuantity(productId: number | string, quantity: number) {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      item.quantity = quantity
      if (item.quantity <= 0) {
        removeItem(productId)
      } else {
        saveToLocalStorage()
        if (authStore.isAuthenticated) {
          saveToBackend()
        }
      }
    }
  }

  function clearCart() {
    items.value = []
    selectedShipping.value = null
    shippingAddress.value = null

    saveToLocalStorage()
    if (authStore.isAuthenticated) {
      saveToBackend()
    }
  }

  function setShipping(shipping: ShippingOption | null) {
    selectedShipping.value = shipping

    saveToLocalStorage()
    if (authStore.isAuthenticated) {
      saveToBackend()
    }
  }

  function setShippingAddress(address: ShippingAddress | null) {
    shippingAddress.value = address

    saveToLocalStorage()
    if (authStore.isAuthenticated) {
      saveToBackend()
    }
  }

  // Observador para salvar alterações automaticamente
  watch(() => authStore.isAuthenticated, syncCartWithAuth)

  // Inicializa o carrinho
  initCart()

  return {
    items,
    totalItems,
    totalAmount,
    cartTotal,
    selectedShipping,
    shippingAddress,
    isLoading,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    setShipping,
    setShippingAddress,
    saveToLocalStorage,
    loadFromLocalStorage,
    saveToBackend,
    loadFromBackend,
    syncCartWithAuth,
    initCart
  }
}) 