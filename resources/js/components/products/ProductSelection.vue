<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title title="Selección de productos"/>
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
        <v-card-text>
          <v-row dense>
            <v-col cols="12">
              <v-select
                :items="sizeTypes"
                item-text="name"
                v-model="sizeType"
                label="Tipo de talla"
                prepend-icon="mdi-human-male-boy"
                return-object
                hide-details
                @change="fetchProductNames"
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                :items="productNames"
                item-text="product_name"
                v-model="productName"
                label="Nombre"
                prepend-icon="mdi-hanger"
                return-object
                persistent-hint
                :hint="productName.category_name"
                :disabled="Object.keys(sizeType).length === 0"
                @change="fetchGenders"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-select
                :items="genders"
                item-text="name"
                v-model="gender"
                label="Género"
                prepend-icon="mdi-gender-male-female"
                return-object
                hide-details
                :disabled="Object.keys(productName).length === 0"
                @change="fetchBrands"
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                :items="brands"
                item-text="name"
                v-model="brand"
                label="Marca"
                prepend-icon="mdi-shopping-outline"
                return-object
                hide-details
                :disabled="Object.keys(gender).length === 0"
                @change="fetchColors"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-autocomplete
                :items="colors"
                item-text="name"
                v-model="color"
                label="Color"
                prepend-icon="mdi-invert-colors"
                return-object
                hide-details
                :disabled="Object.keys(brand).length === 0"
                @change="fetchSizes"
              ></v-autocomplete>
            </v-col>
            <v-col cols="12">
              <v-row  align="center" align-content="space-between" dense>
                <v-col cols="11">
                  <v-select
                    :items="sizes"
                    item-text="size_name"
                    return-object
                    label="Tallas"
                    multiple
                    v-model="products"
                    prepend-icon="mdi-tshirt-crew-outline"
                    chips
                    deletable-chips
                    hide-selected
                    :disabled="Object.keys(color).length === 0"
                  ></v-select>
                </v-col>
                <v-col cols="1">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        x-small
                        fab
                        color="success"
                        v-bind="attrs"
                        v-on="on"
                        :disabled="Object.keys(color).length === 0"
                        @click="products = sizes"
                      >
                        <v-icon>mdi-check-all</v-icon>
                      </v-btn>
                    </template>
                    <span>Seleccionar todo</span>
                  </v-tooltip>
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-row dense justify="end">
            <v-col cols="12" md="6">
              <v-btn
                block
                color="info"
                :disabled="products.length == 0"
                @click.stop="selectProducts"
              >
                Aceptar
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProductSelection',
  props: {
    store: {
      type: Object,
      required: true,
    },
    movementType: {
      type: Object,
      required: true,
    },
    available: {
      type: Boolean,
      required: true,
    },
  },
  data: function() {
    return {
      dialog: false,
      active: 0,
      excluded: [],
      sizeType: {},
      sizeTypes: [],
      productName: {},
      productNames: [],
      gender: {},
      genders: [],
      brand: {},
      brands: [],
      color: {},
      colors: [],
      sizes: [],
      products: [],
    }
  },
  methods: {
    showDialog(excluded) {
      this.dialog = true
      this.sizeType = {}
      this.excluded = excluded
      this.clearSelection()
    },
    clearSelection() {
      if (this.movementType.code == 'ENTRY') {
        this.active = 0
      } else {
        this.active = 1
      }
      this.sizeType = {}
      this.sizeTypes = []
      this.productName = {}
      this.productNames = []
      this.gender = {}
      this.genders = []
      this.brand = {}
      this.brands = []
      this.color = {}
      this.colors = []
      this.sizes = []
      this.products = []
      this.$nextTick(() => {
        this.fetchSizeTypes()
      })
    },
    async fetchSizeTypes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_selection/size_types', {
          params: {
            active: this.active,
            store_id: this.store == {} ? null : this.store.id,
          },
        })
        this.sizeTypes = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchProductNames() {
      this.productName = {}
      this.productNames = []
      this.gender = {}
      this.genders = []
      this.brand = {}
      this.brands = []
      this.color = {}
      this.colors = []
      this.sizes = []
      this.products = []
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_selection/product_names', {
          params: {
            active: this.active,
            store_id: this.store == {} ? null : this.store.id,
            size_type_id: this.sizeType.id,
          }
        })
        this.productNames = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchGenders() {
      this.gender = {}
      this.genders = []
      this.brand = {}
      this.brands = []
      this.color = {}
      this.colors = []
      this.sizes = []
      this.products = []
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_selection/genders', {
          params: {
            active: this.active,
            store_id: this.store == {} ? null : this.store.id,
            size_type_id: this.sizeType.id,
            product_name_id: this.productName.product_name_id,
          }
        })
        this.genders = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchBrands() {
      this.brand = {}
      this.brands = []
      this.color = {}
      this.colors = []
      this.sizes = []
      this.products = []
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_selection/brands', {
          params: {
            active: this.active,
            store_id: this.store == {} ? null : this.store.id,
            size_type_id: this.sizeType.id,
            product_name_id: this.productName.product_name_id,
            gender_id: this.gender.id,
          }
        })
        this.brands = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchColors() {
      this.color = {}
      this.colors = []
      this.sizes = []
      this.products = []
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_selection/colors', {
          params: {
            active: this.active,
            store_id: this.store == {} ? null : this.store.id,
            size_type_id: this.sizeType.id,
            product_name_id: this.productName.product_name_id,
            gender_id: this.gender.id,
            brand_id: this.brand.id,
          }
        })
        this.colors = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchSizes() {
      this.sizes = []
      this.products = []
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_selection/sizes', {
          params: {
            active: this.active,
            store_id: this.store == {} ? null : this.store.id,
            size_type_id: this.sizeType.id,
            product_name_id: this.productName.product_name_id,
            gender_id: this.gender.id,
            brand_id: this.brand.id,
            color_id: this.color.id,
            excluded: this.excluded,
            available: this.available ? 1 : 0,
          }
        })
        this.sizes = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    selectProducts() {
      this.products.forEach(product => {
        product.stock = 1
      })
      this.$emit('updateList', {
        productNameId: this.productName.product_name_id,
        productName: this.productName.product_name,
        sizeTypeId: this.sizeType.id,
        sizeTypeName: this.sizeType.name,
        categoryId: this.productName.category_id,
        categoryName: this.productName.category_name,
        genderId: this.gender.id,
        genderName: this.gender.name,
        brandId: this.brand.id,
        brandName: this.brand.name,
        colorId: this.color.id,
        colorName: this.color.name,
        products: this.products,
      })
      this.dialog = false
    }
  },
}
</script>
