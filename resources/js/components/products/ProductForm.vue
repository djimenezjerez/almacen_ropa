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
        <tool-bar-title :title="readOnly ? 'Datos de producto' : 'Agregar producto'"/>
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
        <validation-observer ref="productObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit" :readonly="readOnly">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <v-select
                    label="Tipo de talla"
                    v-model="sizeType"
                    item-text="name"
                    :items="sizeTypes"
                    prepend-icon="mdi-human-male-boy"
                    :return-object="true"
                    disabled
                  ></v-select>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="name"
                    rules="required|min:3"
                  >
                    <v-combobox
                      label="Nombre"
                      v-model="productForm.name"
                      item-text="name"
                      item-value="name"
                      :items="names"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-hanger"
                      :return-object="false"
                      autofocus
                    ></v-combobox>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="sell_price"
                    rules="required|min_value:1.0|max_value:9999999999.99"
                  >
                    <v-text-field
                      label="Precio de venta"
                      v-model.number="productForm.sell_price"
                      data-vv-name="sell_price"
                      :error-messages="errors"
                      prepend-icon="mdi-currency-usd"
                      type="number"
                      step=".5"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="category_name"
                    rules="required"
                  >
                    <v-combobox
                      label="Categoría"
                      v-model="productForm.category_name"
                      item-text="name"
                      item-value="name"
                      :items="categories"
                      data-vv-name="category_name"
                      :error-messages="errors"
                      prepend-icon="mdi-format-list-bulleted-type"
                      :return-object="false"
                    ></v-combobox>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="gender_id"
                    rules="required"
                  >
                    <v-select
                      label="Género"
                      v-model="productForm.gender_id"
                      item-text="name"
                      item-value="id"
                      :items="genders"
                      data-vv-name="gender_id"
                      :error-messages="errors"
                      prepend-icon="mdi-gender-male-female"
                      :return-object="false"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <v-select
                    label="Estándar de talla"
                    v-model="sizeStandard"
                    item-text="name"
                    item-value="value"
                    :items="sizeStandards"
                    prepend-icon="mdi-human-male-height-variant"
                    :return-object="false"
                    @change="productForm.sizes = []"
                  ></v-select>
                </v-col>
                <v-col cols="12">
                  <v-row  align="center" align-content="space-between" dense>
                    <v-col cols="10">
                      <validation-provider
                        v-slot="{ errors }"
                        name="sizes"
                        rules="required|min:1"
                      >
                        <v-select
                          :items="filteredSizes()"
                          item-text="name"
                          item-value="id"
                          label="Tallas"
                          multiple
                          v-model="productForm.sizes"
                          data-vv-name="sizes"
                          :error-messages="errors"
                          prepend-icon="mdi-tshirt-crew-outline"
                          chips
                          deletable-chips
                          hide-selected
                        ></v-select>
                      </validation-provider>
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
                            @click="productForm.sizes = filteredSizes().map(o => o.id)"
                            :disabled="readOnly"
                          >
                            <v-icon>mdi-check-all</v-icon>
                          </v-btn>
                        </template>
                        <span>Seleccionar todo</span>
                      </v-tooltip>
                    </v-col>
                    <v-col cols="1">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            x-small
                            fab
                            color="info"
                            v-bind="attrs"
                            v-on="on"
                            @click="$refs.sizeForm.showDialog(sizeType.id, sizeStandard == 'numeric')"
                            :disabled="readOnly"
                          >
                            <v-icon>mdi-plus</v-icon>
                          </v-btn>
                        </template>
                        <span>Nueva talla numérica</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12">
                  <v-row  align="center" align-content="space-between" dense>
                    <v-col cols="10">
                      <validation-provider
                        v-slot="{ errors }"
                        name="brands"
                        rules="required|min:1"
                      >
                        <v-autocomplete
                          :items="brands"
                          item-text="name"
                          item-value="id"
                          label="Marcas"
                          multiple
                          v-model="productForm.brands"
                          data-vv-name="brands"
                          :error-messages="errors"
                          prepend-icon="mdi-shopping-outline"
                          chips
                          deletable-chips
                          hide-selected
                        ></v-autocomplete>
                      </validation-provider>
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
                            @click="productForm.brands = brands.map(o => o.id)"
                            :disabled="readOnly"
                          >
                            <v-icon>mdi-check-all</v-icon>
                          </v-btn>
                        </template>
                        <span>Seleccionar todo</span>
                      </v-tooltip>
                    </v-col>
                    <v-col cols="1">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            x-small
                            fab
                            color="info"
                            v-bind="attrs"
                            v-on="on"
                            @click="$refs.brandForm.showDialog()"
                            :disabled="readOnly"
                          >
                            <v-icon>mdi-plus</v-icon>
                          </v-btn>
                        </template>
                        <span>Nueva marca</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12">
                  <v-row  align="center" align-content="space-between" dense>
                    <v-col cols="10">
                      <validation-provider
                        v-slot="{ errors }"
                        name="colors"
                        rules="required|min:1"
                      >
                        <v-autocomplete
                          :items="colors"
                          item-text="name"
                          item-value="id"
                          label="Colores"
                          multiple
                          v-model="productForm.colors"
                          data-vv-name="colors"
                          :error-messages="errors"
                          prepend-icon="mdi-invert-colors"
                          chips
                          deletable-chips
                          hide-selected
                        ></v-autocomplete>
                      </validation-provider>
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
                            @click="productForm.colors = colors.map(o => o.id)"
                            :disabled="readOnly"
                          >
                            <v-icon>mdi-check-all</v-icon>
                          </v-btn>
                        </template>
                        <span>Seleccionar todo</span>
                      </v-tooltip>
                    </v-col>
                    <v-col cols="1">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            x-small
                            fab
                            color="info"
                            v-bind="attrs"
                            v-on="on"
                            @click="$refs.colorForm.showDialog()"
                            :disabled="readOnly"
                          >
                            <v-icon>mdi-plus</v-icon>
                          </v-btn>
                        </template>
                        <span>Nuevo color</span>
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
                    type="submit"
                    color="success"
                    :disabled="invalid"
                    v-if="!readOnly"
                  >
                    Guardar
                  </v-btn>
                  <v-btn
                    block
                    color="error"
                    v-else
                    @click.stop="dialog = false"
                  >
                    Cerrar
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-actions>
          </v-form>
        </validation-observer>
      </div>
    </v-card>
    <size-form ref="sizeForm" v-on:updateSizes="addSize"/>
    <color-form ref="colorForm" v-on:updateColors="addColor"/>
    <brand-form ref="brandForm" v-on:updateBrands="addBrand"/>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProductForm',
  props: {
    sizeTypes: {
      type: Array,
      required: true
    },
  },
  components: {
    'size-form': () => import('@/components/products/SizeForm.vue'),
    'color-form': () => import('@/components/products/ColorForm.vue'),
    'brand-form': () => import('@/components/products/BrandForm.vue'),
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      names: [],
      categories: [],
      brands: [],
      sizeType: null,
      genders: [],
      sizes: [],
      colors: [],
      sizeStandards: [
        {
          name: 'Tallas numéricas',
          value: 'numeric'
        }, {
          name: 'Tallas alfabéticas',
          value: 'alphabetic'
        },
      ],
      sizeStandard: 'numeric',
      productForm: {
        id: null,
        name: null,
        sell_price: null,
        category_name: null,
        gender_id: null,
        sizes: [],
        brands: [],
        colors: [],
      },
    }
  },
  mounted() {
    this.fetchNames()
  },
  methods: {
    filteredSizes() {
      if (this.sizeTypes.length > 0) {
        const standard = this.sizeStandard == 'numeric'
        return this.sizes.filter(o => (o.numeric == standard && o.size_type_id == this.sizeType.id))
      } else {
        return []
      }
    },
    addBrand(param) {
      this.brands.push(param)
      this.productForm.brands.push(param.id)
    },
    addColor(param) {
      this.colors.push(param)
      this.productForm.colors.push(param.id)
    },
    addSize(param) {
      this.sizes.push(param)
      this.productForm.sizes.push(param.id)
    },
    showDialog(sizeType, product = null, readOnly = false) {
      this.readOnly = readOnly
      this.sizeType = sizeType
      if (product) {
        this.fetchProduct(product.product_name_id)
      } else {
        this.productForm = {
          id: null,
          name: null,
          sell_price: null,
          category_name: null,
          gender_id: null,
          sizes: [],
          brands: [],
          colors: [],
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.productObserver.reset()
      })
    },
    async fetchNames() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product_name')
        this.names = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchCategories()
      }
    },
    async fetchCategories() {
      try {
        let response = await axios.get('category', {
          params: {
            combo: true,
          }
        })
        this.categories = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchBrands()
      }
    },
    async fetchBrands() {
      try {
        let response = await axios.get('brand')
        this.brands = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchGenders()
      }
    },
    async fetchGenders() {
      try {
        let response = await axios.get('gender')
        this.genders = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchSizes()
      }
    },
    async fetchSizes() {
      try {
        let response = await axios.get('size')
        this.sizes = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchColors()
      }
    },
    async fetchColors() {
      try {
        let response = await axios.get('color')
        this.colors = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchProduct(id) {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`product/${id}`)
        this.productForm = response.data.product
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async submit() {
      try {
        this.$store.dispatch('loading', true)
        let valid = await this.$refs.productObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          const response = await axios.post('product', this.productForm)
          this.$toast.success(response.data.message)
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.productObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.productObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
