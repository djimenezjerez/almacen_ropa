import Vue from 'vue'
import store from '@/plugins/store.js'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes: [
    {
      path: '*',
      redirect: {
        name: 'root',
      }
    }, {
      path: '/login',
      name: 'login',
      component: () => import('@/views/Login.vue'),
    }, {
      path: '/',
      name: 'root',
      component: () => import('@/layouts/Main.vue'),
      children: [
        {
          path: '/profile',
          name: 'profile',
          component: () => import('@/views/Profile.vue'),
        }, {
          path: '/dashboard',
          name: 'dashboard',
          component: () => import('@/views/Dashboard.vue'),
        }, {
          path: '/users',
          name: 'users',
          component: () => import('@/views/Users.vue'),
        }, {
          path: '/stores',
          name: 'stores',
          component: () => import('@/views/Stores.vue'),
        }, {
          path: '/warehouses',
          name: 'warehouses',
          component: () => import('@/views/Warehouses.vue'),
        }, {
          path: '/:storeType/:storeId/employees',
          name: 'employees',
          component: () => import('@/views/Employees.vue'),
        }, {
          path: '/suppliers',
          name: 'suppliers',
          component: () => import('@/views/Suppliers.vue'),
        }, {
          path: '/clients',
          name: 'clients',
          component: () => import('@/views/Clients.vue'),
        }, {
          path: '/:storeType/:storeId/products',
          name: 'inventory',
          component: () => import('@/views/Products.vue'),
        }, {
          path: '/:storeType/:storeId/products/:productNameId',
          name: 'inventoryDetails',
          component: () => import('@/components/products/ProductDetails.vue'),
        }, {
          path: '/:storeType/:storeId/products/:productNameId/sizes/:productId',
          name: 'inventorySizes',
          component: () => import('@/components/products/ProductSizes.vue'),
        }, {
          path: '/products',
          name: 'products',
          component: () => import('@/views/Products.vue'),
        }, {
          path: '/products/:productNameId',
          name: 'productDetails',
          component: () => import('@/components/products/ProductDetails.vue'),
        }, {
          path: '/products/:productNameId/sizes/:productId',
          name: 'productSizes',
          component: () => import('@/components/products/ProductSizes.vue'),
        }, {
          path: '/categories',
          name: 'categories',
          component: () => import('@/views/Categories.vue'),
        }, {
          path: '/movements',
          name: 'movements',
          component: () => import('@/views/Movements.vue'),
        }, {
          path: '/movements/ENTRY',
          name: 'movementEntry',
          component: () => import('@/components/movements/MovementEntry.vue'),
        }, {
          path: '/movements/ADJUSTMENT',
          name: 'movementEntry',
          component: () => import('@/components/movements/MovementAdjustment.vue'),
        }, {
          path: '/movements/TRANSFER',
          name: 'movementTransfer',
          component: () => import('@/components/movements/MovementTransfer.vue'),
        }, {
          path: '/movements/:movementId',
          name: 'movementDetails',
          component: () => import('@/components/movements/MovementDetails.vue'),
        },
      ]
    },
  ]
})

router.beforeEach((to, from, next) => {
  if (to.name != 'login') {
    if (store.getters.isLoggedIn) {
      if (to.name == 'root') {
        next({
          name: 'dashboard',
        })
      } else {
        next()
      }
    } else {
      next({
        name: 'login',
      })
    }
  } else {
    if (store.getters.isLoggedIn) {
      next({
        name: 'root',
      })
    } else {
      next()
    }
  }
})

export default router
