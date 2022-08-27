<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="stockTransferObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="type"
                    rules="required|alpha"
                  >
                    <v-select
                      :items="types"
                      item-text="display_name"
                      item-value="name"
                      label="Tipo de movimiento"
                      v-model="type"
                      data-vv-name="type"
                      :error-messages="errors"
                      prepend-icon="mdi-file-document"
                    ></v-select>
                  </validation-provider>
                  <validation-provider
                    v-slot="{ errors }"
                    name="origin_store_id"
                    rules="required|boolean"
                  >
                    <v-select
                      :items="types"
                      item-text="display_name"
                      item-value="name"
                      label="Tipo de documento"
                      v-model="stockTransferForm.origin_store_id"
                      data-vv-name="origin_store_id"
                      :error-messages="errors"
                      prepend-icon="mdi-file-document"
                    ></v-select>
                  </validation-provider>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-row dense justify="end">
                <v-col cols="12" sm="6" md="3">
                  <v-btn
                    block
                    type="submit"
                    color="info"
                    :disabled="invalid || products.length == 0"
                  >
                    Guardar
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-actions>
          </v-form>
        </validation-observer>
      </div>
    </v-card>
  </v-container>
</template>

<script>
export default {
  name: 'StockTransferForm',
  data() {
    return {
      breadcrumbs: [
        {
          text: 'Transferencias de stock',
          disabled: false,
          to: {
            path: '/stock_transfers',
          },
        }, {
          text: 'Nuevo',
          disabled: true,
          to: {
            path: '/stock_transfer_form',
          },
        },
      ],
      types: [
        {
          name: 'transfer',
          display_name: 'Traspaso de stock',
        }, {
          name: 'entry',
          display_name: 'Ingreso de stock',
        }, {
          name: 'adjustment',
          display_name: 'Ajuste de stock',
        }
      ],
      storeTypes: [
        {
          name: 'store',
          display_name: 'Tienda',
        }, {
          name: 'warehouse',
          display_name: 'Warehouse',
        }
      ],
      type: 'transfer',
      originType: 'store',
      destinyType: 'store',
      stores: [],
      warehouses: [],
      products: [],
      stockTransferForm: {
        origin_store_id: null,
        destiny_store_id: null,
        origin_warehouse_id: null,
        destiny_warehouse_id: null,
        products: []
      }
    }
  },
  created() {
    this.fetchStores()
    this.fetchWarehouses()
    this.fetchProducts()
  },
  methods: {
    async fetchStores() {
      try {
        let response = await axios.get(`store`, {
          params: {
            combo: true,
          }
        })
        this.stores = response.data.payload.store
      } catch(error) {
        console.error(error)
      }
    },
    async fetchWarehouses() {
      try {
        let response = await axios.get(`warehouse`, {
          params: {
            combo: true,
          }
        })
        this.warehouses = response.data.payload.store
      } catch(error) {
        console.error(error)
      }
    },
    async fetchProducts() {
      try {
        let response = await axios.get(`product`, {
          params: {
            combo: true,
          }
        })
        this.products = response.data.payload.store
      } catch(error) {
        console.error(error)
      }
    },
  },
}
</script>
