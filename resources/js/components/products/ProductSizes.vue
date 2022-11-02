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
      </v-toolbar>
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
          <div class="font-weight-bold">{{ productName.name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Stock: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ productName.total }}</div>
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
        <v-col cols="4" md="2">
          <div class="text-right">Marca: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ brand.name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Color: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ color.name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Género: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ gender.name }}</div>
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
  },
  data() {
    return {
      breadcrumbs: [
        {
          text: 'Productos',
          disabled: false,
          to: {
            path: '/products',
          },
        }, {
          text: 'Detalle',
          disabled: false,
          to: {
            path: '/product_details',
            query: {
              product_name_id: this.$route.query.product_name_id,
              size_type_id: this.$route.query.size_type_id,
            },
          },
        }, {
          text: 'Tallas',
          disabled: true,
          to: {
            path: '/product_sizes',
            query: {
              product_id: this.$route.query.product_id,
              size_type_id: this.$route.query.size_type_id,
              product_name_id: this.$route.query.product_name_id,
              brand_id: this.$route.query.brand_id,
              gender_id: this.$route.query.gender_id,
              color_id: this.$route.query.color_id,
            },
          },
        },
      ],
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortDesc: []
      },
      totalItems: 0,
      productName: {},
      sizeType: {},
      brand: {},
      gender: {},
      color: {},
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
          width: '9%',
          class: this.$headerClass,
        },
      ],
    }
  },
  created() {
    this.fetchProductName()
    this.fetchSizeType()
    this.fetchBrand()
    this.fetchGender()
    this.fetchColor()
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
    async fetchProductName() {
      try {
        let response = await axios.get(`product_name/${this.$route.query.product_name_id}`, {
          params: {
            size_type_id: this.$route.query.size_type_id,
          }
        })
        this.productName = response.data.payload
      } catch(error) {
        console.error(error)
      }
    },
    async fetchSizeType() {
      try {
        let response = await axios.get(`size_type/${this.$route.query.size_type_id}`)
        this.sizeType = response.data.payload
      } catch(error) {
        console.error(error)
      }
    },
    async fetchBrand() {
      try {
        let response = await axios.get(`brand/${this.$route.query.brand_id}`)
        this.brand = response.data.payload
      } catch(error) {
        console.error(error)
      }
    },
    async fetchGender() {
      try {
        let response = await axios.get(`gender/${this.$route.query.gender_id}`)
        this.gender = response.data.payload
      } catch(error) {
        console.error(error)
      }
    },
    async fetchColor() {
      try {
        let response = await axios.get(`color/${this.$route.query.color_id}`)
        this.color = response.data.payload
      } catch(error) {
        console.error(error)
      }
    },
    async fetchSizes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`product/${this.$route.query.product_id}/sizes`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
            size_type_id: this.$route.query.size_type_id,
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
