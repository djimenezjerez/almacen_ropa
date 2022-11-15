<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
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
          <div class="text-right">Stock total: </div>
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
                      @click="gotoProductSizes(item.id, sizeType.id, item.product_name_id, item.brand_id, item.gender_id, item.color_id)"
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
  data() {
    return {
      breadcrumbs: [
        {
          text: 'Productos',
          disabled: false,
          to: {
            path: '/products',
            query: {
              building_id: this.$route.query.building_id,
              building_type: this.$route.query.building_type,
            },
          },
        }, {
          text: 'Detalle',
          disabled: true,
          to: {
            path: '/product_details',
            query: {
              building_id: this.$route.query.building_id,
              building_type: this.$route.query.building_type,
              product_name_id: this.$route.query.product_name_id,
              size_type_id: this.$route.query.size_type_id,
            },
          },
        },
      ],
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
    gotoProductSizes(productId, sizeTypeId, productNameId, brandId, genderId, colorId) {
      this.$router.push({
        path: '/product_sizes',
        query: {
          building_id: this.$route.query.building_id,
          building_type: this.$route.query.building_type,
          product_id: productId,
          size_type_id: sizeTypeId,
          product_name_id: productNameId,
          brand_id: brandId,
          gender_id: genderId,
          color_id: colorId,
        }
      })
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
    async fetchProducts() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`product/${this.$route.query.product_name_id}`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
            size_type_id: this.$route.query.size_type_id,
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
