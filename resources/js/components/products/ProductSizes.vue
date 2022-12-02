<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[2].to">{{ breadcrumbs[2].text }}</router-link>
        <span class="white--text px-3" v-if="isBuilding">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[3].to" v-if="isBuilding">{{ breadcrumbs[3].text }}</router-link>
      </v-toolbar>
      <building-details v-if="isBuilding" :building="store"/>
      <v-row
        class="background pb-0 pt-2 px-4 mx-0"
        align="center"
        justify="start"
        dense
      >
        <v-col cols="4" md="2">
          <div class="text-right">Producto: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.product_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Stock: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.total_stock }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Categoría: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.category_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Tipo de talla: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.size_type_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Marca: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.brand_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Color: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.color_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Género: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.gender_name }}</div>
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
            :inputLength="1"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="sizes"
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
          <template v-slot:[`item.active`]="{ item }">
            <v-chip
              :color="isActive(item.active) ? 'success' : 'error'"
              dark
              small
            >
              {{ isActive(item.active) ? 'ACTIVO' : 'INACTIVO' }}
            </v-chip>
          </template>
          <template v-slot:[`item.size_name`]="{ item }">
            <div :class="item.size_numeric ? 'font-weight-bold' : 'font-italic'">
              {{ item.size_name }}
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
                      :color="item.active ? 'error' : 'success'"
                      @click="$refs.productSwitch.showDialog(item)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-list-status
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>{{ item.active ? 'Desactivar' : 'Activar' }}</span>
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
                      @click="$refs.sizeRemove.showDialog(item)"
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
    <size-remove ref="sizeRemove" v-on:updateList="fetchSizes"/>
    <product-switch ref="productSwitch" v-on:updateList="fetchSizes"/>
  </v-container>
</template>

<script>
export default {
  name: 'ProductSizes',
  components: {
    'size-remove': () => import('@/components/products/SizeRemove.vue'),
    'product-switch': () => import('@/components/products/ProductSwitch.vue'),
    'building-details': () => import('@/components/shared/BuildingDetails.vue'),
  },
  data() {
    return {
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortDesc: []
      },
      totalItems: 0,
      store: {},
      product: {},
      sizes: [],
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'id',
          class: this.$headerClass,
        }, {
          text: 'TALLA',
          align: 'center',
          sortable: true,
          value: 'size_name',
          class: this.$headerClass,
        }, {
          text: 'STOCK',
          align: 'center',
          sortable: false,
          value: 'stock',
          class: this.$headerClass,
        }, {
          text: 'ESTADO',
          align: 'center',
          sortable: true,
          value: 'active',
          class: this.$headerClass,
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '80px',
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
          }, {
            text: 'Tallas',
            disabled: true,
            to: {
              path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products/${this.$route.params.productNameId}/sizes/${this.$route.params.productId}`,
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
          }, {
            text: 'Tallas',
            disabled: true,
            to: {
              path: `/products/${this.$route.params.productNameId}/sizes/${this.$route.params.productId}`,
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
    this.fetchProduct()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchSizes()
      }
    },
    search: function() {
      this.options.page = 1
      this.fetchSizes()
    }
  },
  methods: {
    isActive(active) {
      return active == true
    },
    async fetchProduct() {
      try {
        let response = await axios.get(`product/${this.$route.params.productId}/details`, {
          params: {
            size_type_id: this.$route.query.size_type_id,
            store_id: this.$route.params.storeId,
          }
        })
        this.product = response.data.payload
        this.fetchSizes()
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
    async fetchSizes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`product/${this.$route.params.productId}/sizes`, {
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
        this.sizes = response.data.payload.data
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
