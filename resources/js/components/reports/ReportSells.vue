<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Reporte de Productos Vendidos"/>
      </v-toolbar>
      <v-row class="pt-5 px-4">
        <v-col
          cols="12"
          sm="6"
          lg="2"
        >
          <v-menu
            v-model="menuDateFrom"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="computedDateFrom"
                label="Fecha inicial"
                prepend-icon="mdi-calendar"
                dense
                hide-details
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="dateFrom"
              no-title
              @input="menuDateFrom = false"
              @change="fetchProducts"
              :max="$moment().format('YYYY-MM-DD')"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-col
          cols="12"
          sm="6"
          lg="2"
        >
          <v-menu
            v-model="menuDateTo"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="computedDateTo"
                label="Fecha final"
                prepend-icon="mdi-calendar"
                dense
                hide-details
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="dateTo"
              no-title
              @input="menuDateTo = false"
              @change="fetchProducts"
              :max="$moment().format('YYYY-MM-DD')"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-col
          cols="12"
          sm="6"
          offset-lg="2"
          lg="3"
        >
          <v-select
            :label="store.id == 0 ? 'Tienda/Almacén' : (store.warehouse ? 'Almacén' : 'Tienda')"
            v-model="store"
            item-text="name"
            :items="filteredStores"
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
        <v-col cols="12">
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
          />
        </v-col>
      </v-row>
      <v-card-text>
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
                    <th v-if="alphabeticSizes > 0" :colspan="alphabeticSizes" class="text-center blue-grey darken-1 white--text body">TALLAS ALFABETICAS</th>
                    <th v-if="numericSizes > 0" :colspan="numericSizes" class="text-center blue-grey white--text">TALLAS NUMÉRICAS</th>
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
                                @click="$refs.reportProduct.showDialog(sizeType, item, store, 'sells')"
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
          <v-col cols="12" class="text-center" v-else>
            No hay datos disponibles
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>
    <report-product ref="reportProduct" :sizeTypes="sizeTypes"/>
  </v-container>
</template>

<script>
export default {
  name: 'ReportSells',
  components: {
    'report-product': () => import('@/components/reports/ReportProduct.vue'),
  },
  data() {
    return {
      search: null,
      menuDateFrom: false,
      dateFrom: this.$moment().startOf('month').format('YYYY-MM-DD'),
      menuDateTo: false,
      dateTo: this.$moment().format('YYYY-MM-DD'),
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
  computed: {
    filteredStores() {
      return this.stores.filter(o => !o.warehouse)
    },
    computedDateFrom () {
      return this.$moment(this.dateFrom).format('DD/MM/YYYY')
    },
    computedDateTo () {
      return this.$moment(this.dateTo).format('DD/MM/YYYY')
    },
    alphabeticSizes() {
      return this.sizes.filter(o => o.numeric == 0).length
    },
    numericSizes() {
      return this.sizes.filter(o => o.numeric == 1).length
    },
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
        let response = await axios.get('report/sells', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
            size_type_id: this.sizeType.id,
            store_id: this.store.id,
            date_from: this.dateFrom,
            date_to: this.dateTo,
          },
        })
        this.sizes = response.data.payload.sizes
        this.products = response.data.payload.products.data
        this.totalItems = response.data.payload.products.total
        this.options.page = response.data.payload.products.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.products.per_page)
      } catch(error) {
        this.$toast.error(error.response.data.message)
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
