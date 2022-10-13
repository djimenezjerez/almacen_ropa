<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Productos"/>
      </v-toolbar>
      <v-row
        class="pt-4 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
          md="8"
          order="last"
          order-md="first"
        >
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
          />
        </v-col>
        <v-col
          cols="12"
          md="4"
          :class="{
            'text-right': $vuetify.breakpoint.mdAndUp,
          }"
          order="first"
          order-md="last"
        >
          <add-button
            text="Agregar producto"
            :block="$vuetify.breakpoint.smAndDown"
            @click="$refs.productForm.showDialog()"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="products"
          :options.sync="options"
          :server-items-length="totalItems"
          :footer-props="{
            itemsPerPageOptions: [8, 15, 30]
          }"
          :calculate-widths="true"
          dense
        >
          <template v-slot:[`item.product_name_id`]="{ index }">
            {{ $helpers.listIndex(index, options) }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="4">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      @click="$refs.productForm.showDialog(item, true)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-eye
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Ver</span>
                </v-tooltip>
              </v-col>
            </v-row>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <product-form ref="productForm" v-on:updateList="fetchProducts"/>
  </v-container>
</template>

<script>
export default {
  name: 'products',
  components: {
    'product-form': () => import('@/components/products/ProductForm.vue'),
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
      products: [],
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'product_name_id',
        }, {
          text: 'CATEGORÍA',
          align: 'center',
          sortable: false,
          value: 'category_name',
        }, {
          text: 'NOMBRE',
          align: 'center',
          sortable: false,
          value: 'product_name',
        }, {
          text: 'TIPO DE TALLA',
          align: 'center',
          sortable: false,
          value: 'size_type_name',
        }, {
          text: 'STOCK',
          align: 'center',
          sortable: false,
          value: 'total_stock',
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '9%',
        },
      ],
    }
  },
  mounted() {
    if (!this.$vuetify.breakpoint.xs) {
      const table = document.getElementById('datatable').getElementsByTagName('table')[0]
      table.setAttribute('class', 'datatables')
      table.setAttribute('width', '100%')
    }
  },
  created() {
    this.fetchProducts()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProducts()
      }
    },
    search: function() {
      this.fetchProducts()
    }
  },
  methods: {
    async fetchProducts() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.products = response.data.payload.data
        this.totalItems = response.data.payload.total
        this.options.page = response.data.payload.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.per_page)
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
