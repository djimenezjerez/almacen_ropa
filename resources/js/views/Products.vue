<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <div v-if="isBuilding">
          <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
          <span class="white--text px-3">/</span>
          <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
        </div>
        <tool-bar-title title="Productos" v-else/>
      </v-toolbar>
      <building-details v-if="isBuilding" :building="store"/>
      <v-row
        class="pt-4 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
          sm="12"
          :md="isBuilding ? 9 : 12"
          :xl="isBuilding ? 10 : 8"
        >
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
          />
        </v-col>
        <v-col
          cols="12"
          :sm="isBuilding ? 12 : 5"
          md="3"
          xl="2"
          :class="{
            'text-right': $vuetify.breakpoint.smAndUp,
          }"
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
        <v-col
          cols="12"
          sm="7"
          md="9"
          xl="2"
          :class="{
            'text-right': $vuetify.breakpoint.smAndUp,
          }"
          v-if="!isBuilding"
        >
          <add-button
            text="Agregar producto"
            :block="$vuetify.breakpoint.xs"
            @click="$refs.productForm.showDialog(sizeType)"
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
        >
          <template v-slot:[`item.product_name_id`]="{ index }">
            {{ $helpers.listIndex(index, options) }}
          </template>
          <template v-slot:[`item.sell_price`]="{ item }">
            {{ item.sell_price.toFixed(2) }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="12">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      @click="gotoProductDetails(item.product_name_id)"
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
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <product-form ref="productForm" :sizeTypes="sizeTypes" v-on:updateList="fetchProducts"/>
  </v-container>
</template>

<script>
export default {
  name: 'Products',
  components: {
    'product-form': () => import('@/components/products/ProductForm.vue'),
    'building-details': () => import('@/components/shared/BuildingDetails.vue'),
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
      store: {},
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'product_name_id',
          class: this.$headerClass,
        }, {
          text: 'CATEGORÍA',
          align: 'center',
          sortable: false,
          value: 'category_name',
          class: this.$headerClass,
        }, {
          text: 'NOMBRE',
          align: 'center',
          sortable: false,
          value: 'product_name',
          class: this.$headerClass,
        }, {
          text: 'PRECIO',
          align: 'center',
          sortable: false,
          value: 'sell_price',
          class: this.$headerClass,
        }, {
          text: 'STOCK',
          align: 'center',
          sortable: false,
          value: 'total_stock',
          class: this.$headerClass,
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '40px',
          class: this.$headerClass,
        },
      ],
    }
  },
  computed: {
    isBuilding() {
      return (this.$route.params.storeId != undefined)
    },
    isWarehouse() {
      return this.store.warehouse
    },
    breadcrumbs() {
      return [
        {
          text: this.isWarehouse ? 'Almacenes' : 'Tiendas',
          disabled: false,
          to: {
            path: this.isWarehouse ? '/warehouses' : '/stores',
          },
        }, {
          text: 'Inventario',
          disabled: true,
          to: {
            path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products`,
          },
        },
      ]
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
    this.fetchSizeTypes()
  },
  methods: {
    gotoProductDetails(productNameId) {
      this.$router.push({
        path: this.isBuilding ? `/${this.$route.params.storeType}/${this.$route.params.storeId}/products/${productNameId}` : `/products/${productNameId}`,
        query: {
          size_type_id: this.sizeType.id
        }
      })
    },
    async fetchSizeTypes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('size_type')
        this.sizeTypes = response.data.payload.data
        if (this.sizeTypes.length > 0) {
          this.sizeType = this.sizeTypes[0]
          this.fetchProducts()
        }
      } catch(error) {
        console.error(error)
      } finally {
        if (this.isBuilding) {
          this.fetchStore()
        }
      }
    },
    async fetchStore() {
      try {
        let response = await axios.get(`store/${this.$route.params.storeId}`)
        this.store = response.data.payload.store
      } catch(error) {
        console.error(error)
      }
    },
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
            size_type_id: this.sizeType.id,
            store_id: this.$route.params.storeId,
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
