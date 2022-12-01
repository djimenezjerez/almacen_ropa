<template>
  <v-container>
    <v-card>
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="{ path: `/movements` }">Movimientos de stock</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="{ path: `/movements/TRANSFER` }">{{ movementType.name }}</router-link>
      </v-toolbar>
      <building-details :building="$store.getters.store"/>
      <v-row
        class="px-4"
        align="center"
        justify="end"
      >
        <v-col
          cols="12"
          sm="12"
          xl="6"
        >
          <v-text-field
            label="Glosa"
            prepend-icon="mdi-comment-text-outline"
            filled
            outlined
            clearable
            dense
            hide-details
            v-model="comment"
          ></v-text-field>
        </v-col>
        <v-col
          cols="12"
          md="6"
          xl="3"
          :class="{
            'text-right': $vuetify.breakpoint.smAndUp,
          }"
        >
          <v-select
            :label="store.warehouse == undefined ? 'Tienda/Almacén' : (store.warehouse ? 'Almacén' : 'Tienda')"
            v-model="store"
            item-text="name"
            :items="stores"
            prepend-icon="mdi-store"
            return-object
            dense
            hide-details
            @change="products = []"
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          md="6"
          xl="3"
          :class="{
            'text-right': $vuetify.breakpoint.smAndUp,
          }"
        >
          <add-button
            text="Seleccionar productos"
            block
            :disabled="Object.keys(store).length === 0"
            @click="$refs.productSelection.showDialog(products.map(o => o.products.map(i => i.id)).flat())"
          />
        </v-col>
      </v-row>
      <v-card-text v-show="products.length > 0">
        <v-col cols="12">
          <div class="text-h5 font-weight-bold text-center">Productos a Transferir</div>
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
                    <th class="text-center" width="30%">
                      TALLA
                    </th>
                    <th class="text-center" width="25%">
                      STOCK ACTUAL
                    </th>
                    <th class="text-center" width="25%">
                      CANTIDAD
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
                      ></v-text-field>
                    </td>
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
    <product-selection ref="productSelection" :movementType="movementType" :available="true" :store="$store.getters.store" v-on:updateList="updateList"/>
  </v-container>
</template>

<script>
export default {
  name: 'MovementEntry',
  components: {
    'building-details': () => import('@/components/shared/BuildingDetails.vue'),
    'product-selection': () => import('@/components/products/ProductSelection.vue'),
  },
  data() {
    return {
      comment: '',
      products: [],
      movementType: {},
      store: {},
      stores: [],
    }
  },
  mounted() {
    this.fetchMovementType()
  },
  methods: {
    async submit() {
      try {
        this.$store.dispatch('loading', true)
        const response = await axios.post('movement', {
          movement_type_id: this.movementType.id,
          from_store_id: this.$store.getters.store.id,
          to_store_id: this.store.id,
          comment: this.comment,
          details: this.products.map(o => o.products).flat(),
        })
        this.$toast.success(response.data.message)
        this.$router.push({ path: '/movements' })
      } catch(error) {
        this.$toast.error(error.response.data.errors[Object.keys(error.response.data.errors)[0]][0])
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchMovementType() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`movement_type`, {
          params: {
            active: 1,
          },
        })
        this.movementType = response.data.payload.data.find(o => o.code == 'TRANSFER')
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchStores()
      }
    },
    async fetchStores() {
      try {
        let response = await axios.get(`store`, {
          params: {
            combo: true,
            warehouse: 0,
          }
        })
        this.stores = response.data.payload.data.filter(o => o.id != this.$store.getters.store.id)
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
