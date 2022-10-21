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
                <v-col cols="12" lg="4">
                  <fieldset class="px-3">
                    <legend class="px-2">Datos del movimiento</legend>
                    <validation-provider
                      v-slot="{ errors }"
                      name="type"
                      rules="required|alpha"
                    >
                      <v-select
                        :items="types"
                        item-text="displayName"
                        item-value="name"
                        label="Tipo de movimiento"
                        v-model="stockTransferForm.type"
                        data-vv-name="type"
                        :error-messages="errors"
                        prepend-icon="mdi-file-document"
                        @change="changeType"
                      ></v-select>
                    </validation-provider>
                  </fieldset>
                </v-col>
                <v-col cols="12" :lg="stockTransferForm.type == 'transfer' ? 4 : 8" v-if="stockTransferForm.type != 'entry'">
                  <fieldset class="px-3">
                    <legend class="px-2">Origen</legend>
                    <validation-provider
                      v-slot="{ errors }"
                      name="origin_type"
                      rules="required"
                    >
                      <v-select
                        :items="storeTypes"
                        item-text="displayName"
                        item-value="name"
                        v-model="stockTransferForm.origin_type"
                        data-vv-name="origin_type"
                        :error-messages="errors"
                        prepend-icon="mdi-store"
                      ></v-select>
                    </validation-provider>
                    <validation-provider
                      v-slot="{ errors }"
                      name="origin_id"
                      rules="required"
                    >
                      <v-select
                        :items="stockTransferForm.origin_type == 'store' ? stores : warehouses"
                        item-text="name"
                        item-value="id"
                        v-model="stockTransferForm.origin_id"
                        data-vv-name="origin_id"
                        :error-messages="errors"
                        :prepend-icon="stockTransferForm.origin_type == 'store' ? 'mdi-storefront-outline' : 'mdi-package-variant'"
                      ></v-select>
                    </validation-provider>
                  </fieldset>
                </v-col>
                <v-col cols="12" :lg="stockTransferForm.type == 'transfer' ? 4 : 8" v-if="stockTransferForm.type != 'adjustment'">
                  <fieldset class="px-3">
                    <legend class="px-2">Destino</legend>
                    <validation-provider
                      v-slot="{ errors }"
                      name="destiny_type"
                      rules="required"
                    >
                      <v-select
                        :items="storeTypes"
                        item-text="displayName"
                        item-value="name"
                        v-model="stockTransferForm.destiny_type"
                        data-vv-name="destiny_type"
                        :error-messages="errors"
                        prepend-icon="mdi-store"
                      ></v-select>
                    </validation-provider>
                    <validation-provider
                      v-slot="{ errors }"
                      name="destiny_id"
                      rules="required"
                    >
                      <v-select
                        :items="stockTransferForm.destiny_type == 'store' ? stores : warehouses"
                        item-text="name"
                        item-value="id"
                        v-model="stockTransferForm.destiny_id"
                        data-vv-name="destiny_id"
                        :error-messages="errors"
                        :prepend-icon="stockTransferForm.destiny_type == 'store' ? 'mdi-storefront-outline' : 'mdi-package-variant'"
                      ></v-select>
                    </validation-provider>
                  </fieldset>
                </v-col>
                <v-col cols="12">
                  <fieldset class="px-3 pb-3">
                    <legend class="px-2">Productos</legend>
                    <v-row dense justify="end">
                      <v-col cols="12" sm="6" md="3">
                        <add-button
                          text="Agregar productos"
                          :block="true"
                          color="success"
                          @click="$refs.productSelection.showDialog(stockTransferForm.type, stockTransferForm.origin_type, stockTransferForm.origin_id, stockTransferForm.products.map(o => o.products.map(i => i.id)).flat(1))"
                        />
                      </v-col>
                    </v-row>
                    <v-card elevation="3" class="my-2" v-for="group in stockTransferForm.products" :key="`${group.product_name_id}-${group.products[0].id}`">
                      <v-card-title class="pt-0">
                        {{ group.product_name }}
                      </v-card-title>
                      <v-card-subtitle class="pb-0">
                        {{ group.size_type_name }} / {{ group.gender_name }}
                      </v-card-subtitle>
                      <v-card-text>
                        <v-simple-table dense>
                          <template v-slot:default>
                            <thead>
                              <tr>
                                <th width="50%" class="text-center">
                                  Marca
                                </th>
                                <th width="10%" class="text-center">
                                  Talla
                                </th>
                                <th width="20%" class="text-center">
                                  Color
                                </th>
                                <th width="10%" class="text-center">
                                  Stock
                                </th>
                                <th width="10%" class="text-center">
                                  Acciones
                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr
                                v-for="product in group.products"
                                :key="product.id"
                              >
                                <td class="text-center">{{ product.brand_name }}</td>
                                <td class="text-center">{{ product.size_name }}</td>
                                <td class="text-center">{{ product.color_name }}</td>
                                <td class="text-center">
                                  <v-text-field
                                    v-model="product.quantity"
                                    type="number"
                                    hide-details
                                    :min="product.stock"
                                  ></v-text-field>
                                </td>
                                <td class="text-center">
                                  <v-btn
                                    icon
                                    color="error"
                                    @click.stop="removeItem(group.product_name_id, group.category_id, group.size_type_id, group.gender_id, product.id)"
                                  >
                                    <v-icon
                                      dense
                                    >
                                      mdi-close-circle
                                    </v-icon>
                                  </v-btn>
                                </td>
                              </tr>
                            </tbody>
                          </template>
                        </v-simple-table>
                      </v-card-text>
                    </v-card>
                  </fieldset>
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
                    :disabled="invalid || stockTransferForm.products.length == 0"
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
    <modal-product-selection ref="productSelection" v-on:updateList="updateList"/>
  </v-container>
</template>

<script>
export default {
  name: 'StockTransferForm',
  components: {
    'modal-product-selection': () => import('@/components/products/ProductSelection.vue'),
  },
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
          displayName: 'Traspaso de stock',
        }, {
          name: 'entry',
          displayName: 'Ingreso de stock',
        }, {
          name: 'adjustment',
          displayName: 'Ajuste de stock',
        }
      ],
      storeTypes: [
        {
          name: 'store',
          displayName: 'Tienda',
        }, {
          name: 'warehouse',
          displayName: 'Almacén',
        }
      ],
      stores: [],
      warehouses: [],
      stockTransferForm: {
        type: 'transfer',
        origin_type: 'store',
        destiny_type: 'store',
        origin_id: null,
        destiny_id: null,
        products: []
      }
    }
  },
  created() {
    this.fetchStores()
    this.fetchWarehouses()
  },
  methods: {
    changeType(value) {
      if (value == 'entry') {
        this.stockTransferForm.origin_type = null
        this.stockTransferForm.origin_id = null
        this.stockTransferForm.destiny_type = null
        this.stockTransferForm.destiny_id = null
      }
    },
    async submit() {
      try {
        let valid = await this.$refs.stockTransferObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          const response = await axios.post('stock_transfer', this.stockTransferForm)
          this.$toast.success(response.data.message)
          this.$router.push({ path: '/stock_transfers' })
        }
      } catch(error) {
        this.$toast.error(error.response.data.message)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    updateList(selection) {
      const productIndex = this.stockTransferForm.products.findIndex(o => (o.category_id == selection.category_id && o.gender_id == selection.gender_id && o.product_name_id == selection.product_name_id && o.size_type_id == selection.size_type_id))
      if (productIndex >= 0) {
        this.stockTransferForm.products[productIndex].products = this.stockTransferForm.products[productIndex].products.concat(selection.products)
      } else {
        this.stockTransferForm.products.push(selection)
      }
    },
    removeItem(productNameId, categoryId, sizeTypeId, genderId, productId) {
      const groupIndex = this.stockTransferForm.products.findIndex(o => (o.product_name_id == productNameId && o.category_id == categoryId && o.size_type_id == sizeTypeId && o.gender_id == genderId))
      const productIndex = this.stockTransferForm.products[groupIndex].products.findIndex(o => o.id == productId)
      if (this.stockTransferForm.products[groupIndex].products.length == 1) {
        this.stockTransferForm.products.splice(groupIndex, 1)
      } else {
        this.stockTransferForm.products[groupIndex].products.splice(productIndex, 1)
      }
    },
    async fetchStores() {
      try {
        let response = await axios.get(`store`, {
          params: {
            combo: true,
          }
        })
        this.stores = response.data.payload.data
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
        this.warehouses = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
  },
}
</script>
