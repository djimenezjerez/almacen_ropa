<template>
  <v-container>
    <v-card>
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="{ path: `/sells` }">Ventas</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="{ path: `/sells/new` }">Nueva</router-link>
      </v-toolbar>
      <v-row
        class="pt-5 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
          lg="5"
          order="last"
          order-md="first"
        >
          <v-autocomplete
            label="NIT/CI cliente"
            v-model="client"
            item-text="document"
            :items="clients"
            prepend-icon="mdi-account-circle"
            filled
            outlined
            dense
            hide-details
            return-object
          ></v-autocomplete>
        </v-col>
        <v-col
          cols="12"
          md="6"
          lg="3"
        >
          <v-btn
            color="success"
            block
            @click="$refs.clientForm.showDialog()"
          >
            <v-icon
              class="mr-3"
            >
              mdi-account
            </v-icon>
            <div>Nuevo cliente</div>
          </v-btn>
        </v-col>
        <v-col
          cols="12"
          md="6"
          lg="4"
        >
          <add-button
            text="Seleccionar productos"
            :block="true"
            :disabled="Object.keys(client).length === 0"
            @click="$refs.productSelection.showDialog(products.map(o => o.products.map(i => i.id)).flat())"
          />
        </v-col>
      </v-row>
      <v-row
        class="backgroundContrast pb-0 pt-2 px-4 mx-0"
        align="center"
        justify="start"
        dense
        v-show="Object.keys(client).length > 0"
      >
        <v-col cols="4" md="2">
          <div class="text-right">Razón social: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ client.name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Documento: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ client.document_type_code }}</div>
        </v-col>
      </v-row>
      <v-card-text v-show="products.length > 0">
        <v-col cols="12">
          <div class="text-h5 font-weight-bold text-center">Productos</div>
        </v-col>
        <v-row dense v-for="(item, index) in products" :key="index" class="mb-2" style="border: thin solid black; border-radius: 15px;">
          <v-col cols="12">
            <v-row
              class="background"
              align="center"
              justify="start"
              dense
              style="border-radius: 15px 20px 0px 0px;"
            >
              <v-col cols="4" md="2">
                <div class="text-right">Producto: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.productName }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Tipo de talla: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.sizeTypeName }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Categoría: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.categoryName }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Género: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.genderName }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Marca: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.brandName }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Color: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.colorName }}</div>
              </v-col>
            </v-row>
          </v-col>
          <v-col cols="12" class="pa-0">
            <v-simple-table dense style="border-radius: 15px;">
              <template v-slot:default>
                <thead>
                  <tr>
                    <th class="text-center" width="10%">
                      NRO
                    </th>
                    <th class="text-center" width="20%">
                      TALLA
                    </th>
                    <th class="text-center" width="15%">
                      STOCK ACTUAL
                    </th>
                    <th class="text-right" width="15%">
                      PRECIO UNITARIO
                    </th>
                    <th class="text-center" width="15%">
                      CANTIDAD
                    </th>
                    <th class="text-right" width="15%">
                      SUBTOTAL
                    </th>
                    <th class="text-center" width="10%">
                      ACCIONES
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(product, i) in item.products"
                    :key="product.id"
                  >
                    <td class="text-center">{{ i+1 }}</td>
                    <td class="text-center">{{ product.size_name }}</td>
                    <td class="text-center">{{ product.total_stock }}</td>
                    <td class="text-right">{{ product.sell_price.toFixed(2) }}</td>
                    <td>
                      <v-text-field
                        v-model="product.stock"
                        type="number"
                        min="1"
                        :max="product.total_stock"
                        hide-details
                        outlined
                        dense
                        minlength="1"
                        required
                        :class="$helpers.stockExceded(product) ? 'text-input-red' : ''"
                      ></v-text-field>
                    </td>
                    <td class="text-right">{{ (product.sell_price * product.stock).toFixed(2) }}</td>
                    <td class="text-center">
                      <v-btn
                        icon
                        color="error"
                        @click="removeProduct(index, i)"
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
          </v-col>
        </v-row>
        <v-row dense class="mb-2" style="border: thin solid black; border-radius: 15px;">
          <v-col cols="12" class="pa-0">
            <v-simple-table dense style="border-radius: 15px;">
              <template v-slot:default>
                <tbody>
                  <tr>
                    <td colspan="6" class="text-right font-weight-bold" width="75%">TOTAL</td>
                    <td class="text-right font-weight-bold" width="15%">{{ products.map(o => o.products.map(i => i.sell_price * i.stock).reduce((a, b) => a + b, 0)).reduce((a, b) => a + b, 0).toFixed(2) }}</td>
                    <td width="10%"></td>
                  </tr>
                </tbody>
              </template>
            </v-simple-table>
          </v-col>
        </v-row>
      </v-card-text>
      <v-card-actions v-show="products.length > 0">
        <v-row dense justify="end">
          <v-col cols="12" md="4">
            <v-btn
              block
              color="success"
              @click.stop="submit"
            >
              Aceptar
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
    <client-form ref="clientForm" :documentTypes="documentTypes" :cities="cities" v-on:updateList="clientAdded"/>
    <product-selection ref="productSelection" :movementType="movementType" :available="true" :store="$store.getters.store" v-on:updateList="updateList"/>
  </v-container>
</template>

<script>
export default {
  name: 'MovementSell',
  components: {
    'client-form': () => import('@/components/clients/ClientForm.vue'),
    'product-selection': () => import('@/components/products/ProductSelection.vue'),
  },
  data() {
    return {
      documentTypes: [],
      cities: [],
      products: [],
      movementType: {},
      client: {},
      clients: [],
    }
  },
  mounted() {
    this.fetchDocumentTypes()
  },
  methods: {
    submit() {
      try {
        this.$store.dispatch('loading', true)
        let valid = true
        this.products.forEach(item => {
          item.products.forEach(product => {
            if (this.$helpers.stockExceded(product)) {
              valid = false
            }
          })
        })
        this.$nextTick(async() => {
          if (valid) {
            const response = await axios.post('movement', {
              movement_type_id: this.movementType.id,
              from_store_id: this.$store.getters.store.id,
              to_store_id: null,
              client_id: this.client.id,
              comment: null,
              details: this.products.map(o => o.products).flat(),
            })
            this.$toast.success(response.data.message)
            this.$router.push({ path: '/sells' })
          } else {
            this.$toast.error(`La cantidad no puede exceder el stock actual`)
          }
        })
      } catch(error) {
        this.$toast.error(error.response.data.errors[Object.keys(error.response.data.errors)[0]][0])
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchDocumentTypes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('document_type', {
          params: {
            combo: true,
          }
        })
        this.documentTypes = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchCities()
      }
    },
    async fetchCities() {
      try {
        let response = await axios.get('city', {
          params: {
            combo: true,
          }
        })
        this.cities = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchMovementType()
      }
    },
    async fetchMovementType() {
      try {
        let response = await axios.get(`movement_type`, {
          params: {
            active: 0
          }
        })
        this.movementType = response.data.payload.data.find(o => o.code == 'SELL')
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchClients()
      }
    },
    clientAdded(client) {
      this.clients.push(client)
      this.client = client
    },
    async fetchClients() {
      try {
        this.$store.dispatch('loading', true)
        this.client = {}
        let response = await axios.get(`client`, {
          params: {
            combo: true,
          }
        })
        this.clients = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    updateList(product) {
      const index = this.products.findIndex(o => {
        return (o.productNameId == product.productNameId && o.sizeTypeId == product.sizeTypeId && o.categoryId == product.categoryId && o.genderId == product.genderId && o.brandId == product.brandId && o.colorId == product.colorId)
      })
      if (index == -1) {
        this.products.push(product)
      } else {
        this.products[index].products = this.products[index].products.concat(product.products)
      }
    },
    removeProduct(productIndex, sizeIndex) {
      this.products[productIndex].products.splice(sizeIndex, 1)
      if (this.products[productIndex].products.length == 0) {
        this.products.splice(productIndex, 1)
      }
    },
  },
}
</script>
<style scoped>
  .text-input-red /deep/ input {
    color: #f00 !important;
  }
</style>
