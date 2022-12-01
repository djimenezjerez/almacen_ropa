<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
        <span class="white--text px-3" v-if="isBuilding">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[2].to" v-if="isBuilding">{{ breadcrumbs[2].text }}</router-link>
      </v-toolbar>
      <building-details v-if="isBuilding" :building="store"/>
      <v-row
        class="background pb-0 px-4 mx-0"
        align="center"
        justify="start"
        dense
        :class="isBuilding ? '' : 'pt-2'"
      >
        <v-col cols="4" md="2">
          <div class="text-right">Producto: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ productName.name }}</div>
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
        class="px-4"
        align="center"
        justify="start"
        dense
      >
        <v-col
          cols="12"
        >
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
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
          <template v-slot:[`item.id`]="{ index }">
            {{ $helpers.listIndex(index, options) }}
          </template>
          <template v-slot:[`item.gender_name`]="{ item }">
            <div :class="item.gender_name == 'Mujer' ? 'font-italic' : (item.gender_name == 'Unisex' ? '' : 'font-weight-bold')">
              {{ item.gender_name }}
            </div>
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="6">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      @click="gotoProductSizes(item.id)"
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
              <v-col cols="6">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="error"
                      @click="$refs.dialogRemove.showDialog(item)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-close-circle
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Remover</span>
                </v-tooltip>
              </v-col>
            </v-row>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <dialog-remove ref="dialogRemove" :female="true" type="variante" url="product" v-on:updateList="fetchProducts"/>
  </v-container>
</template>

<script>
export default {
  name: 'ProductDetails',
  components: {
    'building-details': () => import('@/components/shared/BuildingDetails.vue'),
  },
  data() {
    return {
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortDesc: [false]
      },
      totalItems: 0,
      productName: {},
      sizeType: {},
      products: [],
      store: {},
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'id',
          class: this.$headerClass,
        }, {
          text: 'COLOR',
          align: 'center',
          sortable: false,
          value: 'color_name',
          class: this.$headerClass,
        }, {
          text: 'GÉNERO',
          align: 'center',
          sortable: false,
          value: 'gender_name',
          class: this.$headerClass,
        }, {
          text: 'MARCA',
          align: 'center',
          sortable: false,
          value: 'brand_name',
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
          width: '9%',
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
      if (this.isBuilding) {
        return [
          {
            text: this.isWarehouse ? 'Almacenes' : 'Tiendas',
            disabled: false,
            to: {
              path: this.isWarehouse ? '/warehouses' : '/stores',
            },
          }, {
            text: 'Inventario',
            disabled: false,
            to: {
              path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products`,
            },
          }, {
            text: 'Detalle',
            disabled: true,
            to: {
              path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products/${this.$route.params.productNameId}`,
              query: {
                size_type_id: this.$route.query.size_type_id,
              }
            },
          },
        ]
      } else {
        return [
          {
            text: 'Productos',
            disabled: false,
            to: {
              path: `/products`,
            },
          }, {
            text: 'Detalle',
            disabled: true,
            to: {
              path: `/products/${this.$route.params.productNameId}`,
              query: {
                size_type_id: this.$route.query.size_type_id,
              }
            },
          },
        ]
      }
    },
  },
  mounted() {
    this.fetchProductName()
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
  methods: {
    gotoProductSizes(productId) {
      this.$router.push({
        path: this.isBuilding ? `/${this.$route.params.storeType}/${this.$route.params.storeId}/products/${this.$route.params.productNameId}/sizes/${productId}` : `/products/${this.$route.params.productNameId}/sizes/${productId}`,
        query: {
          size_type_id: this.sizeType.id,
        }
      })
    },
    async fetchProductName() {
      try {
        let response = await axios.get(`product_name/${this.$route.params.productNameId}`, {
          params: {
            size_type_id: this.$route.query.size_type_id,
            store_id: this.$route.params.storeId,
          }
        })
        this.productName = response.data.payload
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchSizeType()
      }
    },
    async fetchSizeType() {
      try {
        let response = await axios.get(`size_type/${this.$route.query.size_type_id}`)
        this.sizeType = response.data.payload
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
        let response = await axios.get(`product/${this.$route.params.productNameId}`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
            size_type_id: this.$route.query.size_type_id,
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
