<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="800"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title title="Reporte de stock por colores"/>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <v-row
          class="pb-3 pt-2 mx-0"
          align="center"
          justify="start"
          dense
        >
          <v-col cols="4" md="2">
            <div class="text-right">Producto: </div>
          </v-col>
          <v-col cols="8" md="4">
            <div class="font-weight-bold">{{ productName.product_name }}</div>
          </v-col>
          <v-col cols="4" md="2">
            <div class="text-right">Stock total: </div>
          </v-col>
          <v-col cols="8" md="4">
            <div class="font-weight-bold">{{ productName.total_stock }}</div>
          </v-col>
          <v-col cols="4" md="2">
            <div class="text-right">Categoría: </div>
          </v-col>
          <v-col cols="8" md="4">
            <div class="font-weight-bold">{{ productName.category_name }}</div>
          </v-col>
          <v-col cols="4" md="2">
            <div class="text-right">Tipo de talla: </div>
          </v-col>
          <v-col cols="8" md="4">
            <div class="font-weight-bold">{{ sizeType.name }}</div>
          </v-col>
        </v-row>
        <v-row
          class="pb-4 mx-0"
          align="center"
          justify="center"
          dense
          v-show="details.length > 0 && sizes.length > 0"
        >
          <v-simple-table style="border: 1px solid lightgray !important">
            <template v-slot:default>
              <thead>
                <tr>
                  <th :class="`text-center ${$headerClass}`" style="border-right: 1px solid white !important;">
                    Color / Talla
                  </th>
                  <th :class="`text-right ${size.numeric ? 'blue-grey darken-4 white--text body' : 'blue-grey darken-1 white--text body'}`" style="border-right: 1px solid white !important;" v-for="size in sizes" :key="size.id">
                    {{ size.name }}
                  </th>
                  <th :class="`text-right ${$headerClass}`">
                    Total
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="detail in details"
                  :key="detail.color_id"
                >
                  <td class="text-center" style="border-right: 1px solid lightgray !important;">{{ detail.color_name }}</td>
                  <td class="text-right" style="border-right: 1px solid lightgray !important;" v-for="size in sizes" :key="size.id">
                    {{ getStock(size, detail) }}
                  </td>
                  <td class="text-right font-weight-bold">{{ detail.subtotal }}</td>
                </tr>
                <tr>
                  <td class="text-right" style="border-right: 1px solid lightgray !important;" :colspan="sizes.length+1"><v-icon>mdi-sigma</v-icon></td>
                  <td class="text-right font-weight-black">{{ productName.total_stock }}</td>
                </tr>
              </tbody>
            </template>
          </v-simple-table>
        </v-row>
        <v-row dense justify="end">
          <v-col cols="12" md="6">
            <v-btn
              block
              color="error"
              @click.stop="dialog = false"
            >
              Cerrar
            </v-btn>
          </v-col>
        </v-row>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProductReport',
  data: function() {
    return {
      dialog: false,
      sizeType: {
        name: ''
      },
      productName: {
        name: '',
        category_name: '',
      },
      details: [],
      sizes: [],
    }
  },
  methods: {
    getStock(size, detail) {
      if (detail.sizes.some(o => o.id == size.id)) {
        return detail.sizes.find(o => o.id == size.id).stock
      } else {
        return 0
      }
    },
    showDialog(sizeType, productName) {
      this.details = []
      this.sizes = []
      this.sizeType = sizeType
      this.productName = productName
      this.dialog = true
      this.fetchProduct(productName.product_name_id, sizeType.id)
    },
    async fetchProduct(productNameId, sizeTypeId) {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`product/${productNameId}/stock`, {
          params: {
            size_type_id: sizeTypeId,
          }
        })
        this.details = response.data.payload.details
        this.sizes = response.data.payload.sizes
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
  },
}
</script>
