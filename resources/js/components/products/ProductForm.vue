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
                <v-col cols="12" v-if="!$store.getters.loading">
                  <v-tabs
                    v-model="sizeTypeTab"
                    grow
                    @change="changeSizeTypeTab"
                    color="light-blue darken-3"
                  >
                    <v-tab
                      v-for="sizeType in sizeTypes"
                      :key="sizeType.id"
                    >
                      {{ sizeType.name }}
                    </v-tab>
                  </v-tabs>
                  <v-tabs-items v-model="sizeTypeTab">
                    <v-tab-item
                      v-for="sizeType in sizeTypes"
                      :key="sizeType.id"
                    >
                      <v-tabs
                        v-model="genderTab"
                        grow
                        color="light-blue darken-4"
                      >
                        <v-tab
                          v-for="gender in genders"
                          :key="gender.id"
                        >
                          {{ gender.name }}
                        </v-tab>
                      </v-tabs>
                      <v-tabs-items v-model="genderTab">
                        <v-tab-item
                          v-for="gender in genders"
                          :key="gender.id"
                        >
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
                                    item-value="name"
                                    label="Marcas"
                                    multiple
                                    v-model="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.brands"
                                    data-vv-name="brands"
                                    :error-messages="errors"
                                    prepend-icon="mdi-shopping-outline"
                                    chips
                                    deletable-chips
                                    hide-selected
                                    hide-details
                                    dense
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
                                      @click="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.brands = brands.map(o => o.name)"
                                      :disabled="readOnly"
                                    >
                                      <v-icon>mdi-checkbox-outline</v-icon>
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
                                  name="numeric_sizes"
                                  rules="required|min:1"
                                >
                                  <v-select
                                    :items="filteredSizes(true)"
                                    item-text="name"
                                    item-value="name"
                                    label="Tallas numéricas"
                                    multiple
                                    v-model="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.numeric_sizes"
                                    data-vv-name="numeric_sizes"
                                    :error-messages="errors"
                                    prepend-icon="mdi-tshirt-crew-outline"
                                    chips
                                    deletable-chips
                                    hide-selected
                                    hide-details
                                    dense
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
                                      @click="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.numeric_sizes = filteredSizes(true).map(o => o.name)"
                                      :disabled="readOnly"
                                    >
                                      <v-icon>mdi-checkbox-outline</v-icon>
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
                                      @click="$refs.sizeForm.showDialog(sizeTypes[sizeTypeTab].id, true)"
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
                                  name="alphabetic_sizes"
                                  rules="required|min:1"
                                >
                                  <v-select
                                    :items="filteredSizes(false)"
                                    item-text="name"
                                    item-value="name"
                                    label="Tallas alfabéticas"
                                    multiple
                                    v-model="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.alphabetic_sizes"
                                    data-vv-name="alphabetic_sizes"
                                    :error-messages="errors"
                                    prepend-icon="mdi-tshirt-crew"
                                    chips
                                    deletable-chips
                                    hide-selected
                                    hide-details
                                    dense
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
                                      @click="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.alphabetic_sizes = filteredSizes(false).map(o => o.name)"
                                      :disabled="readOnly"
                                    >
                                      <v-icon>mdi-checkbox-outline</v-icon>
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
                                      @click="$refs.sizeForm.showDialog(sizeTypes[sizeTypeTab].id, false)"
                                      :disabled="readOnly"
                                    >
                                      <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                  </template>
                                  <span>Nueva talla alfabética</span>
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
                                    item-value="name"
                                    label="Colores"
                                    multiple
                                    v-model="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.colors"
                                    data-vv-name="colors"
                                    :error-messages="errors"
                                    prepend-icon="mdi-invert-colors"
                                    chips
                                    deletable-chips
                                    hide-selected
                                    hide-details
                                    dense
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
                                      @click="productForm.size_types[sizeTypeTab].genders[genderTab].attributes.colors = colors.map(o => o.name)"
                                      :disabled="readOnly"
                                    >
                                      <v-icon>mdi-checkbox-outline</v-icon>
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
                        </v-tab-item>
                      </v-tabs-items>
                    </v-tab-item>
                  </v-tabs-items>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-row dense justify="end">
                <v-col cols="12" md="6">
                  <v-btn
                    block
                    type="submit"
                    color="info"
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
  components: {
    'size-form': () => import('@/components/products/SizeForm.vue'),
    'color-form': () => import('@/components/products/ColorForm.vue'),
    'brand-form': () => import('@/components/products/BrandForm.vue'),
  },
  data: function() {
    return {
      sizeTypeTab: null,
      genderTab: null,
      dialog: false,
      readOnly: false,
      names: [],
      categories: [],
      brands: [],
      sizeTypes: [],
      sizeNumeric: null,
      genders: [],
      sizes: [],
      colors: [],
      productForm: {
        id: null,
        name: null,
        category_name: null,
        size_types: [],
      },
    }
  },
  created() {
    this.fetchNames()
    this.fetchCategories()
    this.fetchBrands()
    this.fetchSizeTypes()
    this.fetchGenders()
    this.fetchSizes()
    this.fetchColors()
  },
  methods: {
    changeSizeTypeTab() {
      this.$nextTick(() => {
        this.genderTab = 0
      })
    },
    filteredSizes(numeric) {
      if (this.sizeTypes.length > 0) {
        return this.sizes.filter(o => (o.numeric == numeric && o.size_type_id == this.sizeTypes[this.sizeTypeTab].id))
      } else {
        return []
      }
    },
    addBrand(param) {
      this.brands.push(param)
      this.productForm.size_types[this.sizeTypeTab].genders[this.genderTab].attributes.brands.push(param.name)
    },
    addColor(param) {
      this.colors.push(param)
      this.productForm.size_types[this.sizeTypeTab].genders[this.genderTab].attributes.colors.push(param.name)
    },
    addSize(param, numeric) {
      this.sizes.push(param)
      if (numeric) {
        this.productForm.size_types[this.sizeTypeTab].genders[this.genderTab].attributes.numeric_sizes.push(param.name)
      } else {
        this.productForm.size_types[this.sizeTypeTab].genders[this.genderTab].attributes.alphabetic_sizes.push(param.name)
      }
    },
    showDialog(product = null, readOnly = false) {
      this.readOnly = readOnly
      if (product) {
        this.fetchProduct(product.product_name_id)
      } else {
        this.productForm = {
          id: null,
          name: null,
          category_name: null,
          size_types: this.sizeTypes.map(object => {
            return {...object, genders: this.genders.map(item => {
              return  {...item, attributes: {
                brands: [],
                numeric_sizes: [],
                alphabetic_sizes: [],
                colors: [],
              }}
            })}
          })
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.productObserver.reset()
        this.sizeTypeTab = 0
        this.genderTab = 0
      })
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
      }
    },
    async fetchNames() {
      try {
        let response = await axios.get('product_name')
        this.names = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchColors() {
      try {
        let response = await axios.get('color')
        this.colors = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchBrands() {
      try {
        let response = await axios.get('brand')
        this.brands = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchSizeTypes() {
      try {
        let response = await axios.get('size_type')
        this.sizeTypes = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchGenders() {
      try {
        let response = await axios.get('gender')
        this.genders = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchSizes() {
      try {
        let response = await axios.get('size')
        this.sizes = response.data.payload.data
      } catch(error) {
        console.error(error)
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
