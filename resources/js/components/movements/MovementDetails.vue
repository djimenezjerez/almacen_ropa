<template>
  <v-container>
    <v-card>
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="{ path: `/${$route.query.type}` }">{{ $route.query.type == 'movements' ? 'Movimientos de stock' : 'Ventas' }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="{ path: `/${$route.query.type}/${$route.params.movementId}` }">Detalle</router-link>
      </v-toolbar>
      <v-row
        class="backgroundContrast pb-0 pt-2 px-4 mx-0"
        align="center"
        justify="start"
        dense
      >
        <v-col cols="4" md="2">
          <div class="text-right">Movimiento: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ movement.movement_type_name }}</div>
        </v-col>
        <v-col cols="4" md="2" v-if="movement.from_store_id != null">
          <div class="text-right">Desde: </div>
        </v-col>
        <v-col cols="8" md="4" v-if="movement.from_store_id != null">
          <div class="font-weight-bold">{{ movement.from_store_name }}</div>
        </v-col>
        <v-col cols="4" md="2" v-if="movement.to_store_id != null">
          <div class="text-right">Hacia: </div>
        </v-col>
        <v-col cols="8" md="4" v-if="movement.to_store_id != null">
          <div class="font-weight-bold">{{ movement.to_store_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Usuario: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ movement.user_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Fecha: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ movement.created_at | moment('DD/MM/YYYY HH:mm') }}</div>
        </v-col>
        <v-col cols="4" md="2" v-if="movement.client_id != null">
          <div class="text-right">Cliente: </div>
        </v-col>
        <v-col cols="8" md="4" v-if="movement.client_id != null">
          <div class="font-weight-bold">{{ movement.client_name }}</div>
        </v-col>
        <v-col cols="4" md="2" v-if="movement.client_id != null">
          <div class="text-right">{{ movement.client_document_type }}: </div>
        </v-col>
        <v-col cols="8" md="4" v-if="movement.client_id != null">
          <div class="font-weight-bold">{{ movement.client_document }}</div>
        </v-col>
        <v-col cols="4" md="2" v-if="movement.comment">
          <div class="text-right">Glosa: </div>
        </v-col>
        <v-col cols="8" md="4" v-if="movement.comment">
          <div class="font-weight-bold">{{ movement.comment }}</div>
        </v-col>
      </v-row>
      <v-card-text v-if="products.length > 0">
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
                <div class="font-weight-bold">{{ item.product_name }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Tipo de talla: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.size_type_name }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Categoría: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.category_name }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Género: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.gender_name }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Marca: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.brand_name }}</div>
              </v-col>
              <v-col cols="4" md="2">
                <div class="text-right">Color: </div>
              </v-col>
              <v-col cols="8" md="4">
                <div class="font-weight-bold">{{ item.color_name }}</div>
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
                    <th class="text-center" width="45%">
                      TALLA
                    </th>
                    <th class="text-center" width="45%">
                      CANTIDAD
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
                    <td class="text-center">{{ product.stock }}</td>
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
              color="info"
              @click.stop="gotoMovements()"
            >
              Volver
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
export default {
  name: 'MovementDetails',
  data() {
    return {
      movement: {},
      products: [],
    }
  },
  created() {
    this.fetchMovement()
  },
  methods: {
    gotoMovements() {
      this.$router.push({
        path: `/${this.$route.query.type}`,
      })
    },
    async fetchMovement() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`movement/${this.$route.params.movementId}`)
        this.movement = response.data.payload.movement
        this.products = response.data.payload.products
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  }
}
</script>
