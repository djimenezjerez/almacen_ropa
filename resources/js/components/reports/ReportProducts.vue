<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Reporte de Inventario"/>
      </v-toolbar>
      <v-row
        class="pt-4 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
          sm="12"
          lg="6"
          xl="8"
        >
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
          />
        </v-col>
        <v-col
          cols="12"
          sm="6"
          lg="3"
          xl="2"
        >
          <v-select
            :label="store.id == 0 ? 'Tienda/Almacén' : (store.warehouse ? 'Almacén' : 'Tienda')"
            v-model="store"
            item-text="name"
            :items="stores"
            prepend-icon="mdi-store"
            return-object
            dense
            hide-details
            @change="fetchProducts"
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          sm="6"
          lg="3"
          xl="2"
        >
          <v-select
            label="Tipo de talla"
            v-model="sizeType"
            item-text="name"
            :items="sizeTypes"
            prepend-icon="mdi-human-male-boy"
            return-object
            dense
            hide-details
            @change="fetchProducts"
          ></v-select>
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12" v-if="sizes.length > 0">
        <v-data-table
          :items="products"
          :options.sync="options"
          :server-items-length="totalItems"
          :footer-props="{
            itemsPerPageOptions: [8, 15, 30]
          }"
          :calculate-widths="true"
          :mobile-breakpoint="0"
        >
          <template v-slot:header="{}">
            <thead>
              <tr>
                <th rowspan="2" class="text-center blue-grey darken-2 white--text">NRO</th>
                <th rowspan="2" class="text-center blue-grey darken-2 white--text">CATEGORÍA</th>
                <th rowspan="2" class="text-center blue-grey darken-2 white--text">NOMBRE</th>
                <th :colspan="sizes.filter(o => o.numeric == 0).length" class="text-center blue-grey darken-1 white--text body">TALLAS ALFABETICAS</th>
                <th :colspan="sizes.filter(o => o.numeric == 1).length" class="text-center blue-grey white--text">TALLAS NUMÉRICAS</th>
                <th rowspan="2" class="text-center blue-grey darken-2 white--text">TOTAL</th>
                <th rowspan="2" class="text-center blue-grey darken-2 white--text" style="width: 40px; min-width: 40px;">ACCIONES</th>
              </tr>
              <tr>
                <th v-for="(size, index) in sizes" :key="index" class="text-center blue-grey white--text" :class="size.numeric == 0 ? 'darken-1' : ''">{{ size.name }}</th>
              </tr>
            </thead>
          </template>
          <template v-slot:body="{ items }">
            <tbody v-if="items.length > 0">
              <tr v-for="(item, index) in items" :key="index">
                <td class="text-center">{{ $helpers.listIndex(index, options) }}</td>
                <td class="text-center">{{ item.category_name }}</td>
                <td class="text-center">{{ item.product_name }}</td>
                <td class="text-center" v-for="(size, i) in sizes" :key="`${item.product_name_id}-${size.id}`">{{ item.stock[i] }}</td>
                <td class="text-center">{{ item.total_stock }}</td>
                <td class="text-center">
                  <v-row dense no-gutters justify="space-around" align="center">
                    <v-col cols="12">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            icon
                            v-bind="attrs"
                            v-on="on"
                            color="warning"
                            @click="$refs.productReport.showDialog(sizeType, item)"
                          >
                            <v-icon
                              dense
                            >
                              mdi-eye
                            </v-icon>
                          </v-btn>
                        </template>
                        <span>Detalle</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td class="text-center" :colspan="sizes.length + 5">No hay datos disponibles</td>
              </tr>
            </tbody>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <product-report ref="productReport" :sizeTypes="sizeTypes"/>
  </v-container>
</template>

<script>
export default {
  name: 'ReportProducts',
  components: {
    'product-report': () => import('@/components/reports/ReportProduct.vue'),
  },
  data() {
    return {
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: [],
        sortDesc: []
      },
      totalItems: 0,
      sizeTypes: [],
      sizeType: null,
      products: [],
      sizes: [],
      store: {},
      stores: [
        {
          id: 0,
          name: 'Todos',
          warehouse: false
        },
      ],
    }
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProducts()
      }
    },
    search: function() {
      this.options.page = 1
      this.fetchProducts()
    }
  },
  mounted() {
    this.fetchStores()
  },
  methods: {
    async fetchStores() {
      this.store = this.stores[0]
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('store', {
          params: {
            combo: true,
            warehouse: 0,
          },
        })
        this.stores = this.stores.concat(response.data.payload.data)
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchSizeTypes()
      }
    },
    async fetchSizeTypes() {
      try {
        let response = await axios.get('size_type')
        this.sizeTypes = response.data.payload.data
        if (this.sizeTypes.length > 0) {
          this.sizeType = this.sizeTypes[0]
          this.fetchProducts()
        }
      } catch(error) {
        console.error(error)
      }
    },
    async fetchProducts() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('report/products', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
            size_type_id: this.sizeType.id,
            store_id: this.store.id,
          },
        })
        this.sizes = response.data.payload.sizes
        this.products = response.data.payload.products.data
        this.totalItems = response.data.payload.products.total
        this.options.page = response.data.payload.products.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.products.per_page)
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
