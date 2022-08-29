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
        <tool-bar-title :title="readOnly ? 'Datos de producto' : (edit ? 'Editar producto' : 'Agregar producto')"/>
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
                <v-col cols="12" md="6">
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
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="gender_id"
                    rules="required|numeric"
                  >
                    <v-select
                      :items="genders"
                      item-text="name"
                      item-value="id"
                      label="Tipo de talla"
                      v-model="productForm.gender_id"
                      data-vv-name="gender_id"
                      :error-messages="errors"
                      prepend-icon="mdi-gender-male-female"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="brand_name"
                    rules="required"
                  >
                    <v-combobox
                      label="Marca"
                      v-model="productForm.brand_name"
                      item-text="name"
                      item-value="name"
                      :items="brands"
                      data-vv-name="brand_name"
                      :error-messages="errors"
                      prepend-icon="mdi-shopping-outline"
                      :return-object="false"
                    ></v-combobox>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="size_type_id"
                    rules="required|numeric"
                  >
                    <v-select
                      :items="sizeTypes"
                      item-text="name"
                      item-value="id"
                      label="Tamaño"
                      v-model="productForm.size_type_id"
                      data-vv-name="size_type_id"
                      :error-messages="errors"
                      prepend-icon="mdi-human-male-girl"
                      @change="productForm.sizes = []"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="size_standard"
                    rules="required"
                  >
                    <v-select
                      :items="sizeStandards"
                      item-text="name"
                      item-value="numeric"
                      label="Estándar de talla"
                      v-model="sizeNumeric"
                      data-vv-name="size_standard"
                      :error-messages="errors"
                      prepend-icon="mdi-size-xl"
                      @change="productForm.sizes = []"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <v-row  align="center" align-content="space-between" dense>
                    <v-col cols="11">
                      <validation-provider
                        v-slot="{ errors }"
                        name="sizes"
                        rules="required|min:1"
                      >
                        <v-select
                          :items="filteredSizes"
                          item-text="name"
                          item-value="name"
                          label="Tallas"
                          multiple
                          v-model="productForm.sizes"
                          data-vv-name="sizes"
                          :error-messages="errors"
                          prepend-icon="mdi-tshirt-crew-outline"
                        ></v-select>
                      </validation-provider>
                    </v-col>
                    <v-col cols="1">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            outlined
                            x-small
                            fab
                            color="info"
                            v-bind="attrs"
                            v-on="on"
                            :disabled="productForm.size_type_id == null || sizeNumeric === null"
                            @click="$refs.sizeForm.showDialog(productForm.size_type_id, sizeNumeric)"
                          >
                            <v-icon>mdi-plus</v-icon>
                          </v-btn>
                        </template>
                        <span>Nueva talla</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="12">
                  <v-row  align="center" align-content="space-between" dense>
                    <v-col cols="11">
                      <validation-provider
                        v-slot="{ errors }"
                        name="colors"
                        rules="required|min:1"
                      >
                        <v-select
                          :items="colors"
                          item-text="name"
                          item-value="name"
                          label="Colores"
                          multiple
                          v-model="productForm.colors"
                          data-vv-name="colors"
                          :error-messages="errors"
                          prepend-icon="mdi-invert-colors"
                        ></v-select>
                      </validation-provider>
                    </v-col>
                    <v-col cols="1">
                      <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn
                            outlined
                            x-small
                            fab
                            color="info"
                            v-bind="attrs"
                            v-on="on"
                            @click="$refs.colorForm.showDialog()"
                          >
                            <v-icon>mdi-plus</v-icon>
                          </v-btn>
                        </template>
                        <span>Nuevo color</span>
                      </v-tooltip>
                    </v-col>
                  </v-row>
                </v-col>
                <v-col cols="3" v-if="edit">
                  <validation-provider
                    v-slot="{ errors }"
                    name="active"
                    rules="required"
                  >
                    <v-checkbox
                      label="Activo"
                      v-model="productForm.active"
                      data-vv-name="active"
                      :error-messages="errors"
                      prepend-icon="mdi-check-all"
                    ></v-checkbox>
                  </validation-provider>
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
  </v-dialog>
</template>

<script>
export default {
  name: 'ProductForm',
  components: {
    'size-form': () => import('@/components/products/SizeForm.vue'),
    'color-form': () => import('@/components/products/ColorForm.vue'),
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      names: [],
      categories: [],
      brands: [],
      sizeTypes: [],
      sizeStandards: [
        {
          name: 'Numérico',
          numeric: true,
        }, {
          name: 'Alfabético',
          numeric: false,
        }
      ],
      sizeNumeric: null,
      genders: [],
      sizes: [],
      colors: [],
      productForm: {
        id: null,
        name: null,
        active: true,
        category_name: null,
        brand_name: null,
        sizes: [],
        colors: [],
        size_type_id: null,
        gender_id: null,
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
  computed: {
    filteredSizes() {
      if (this.sizeNumeric !== null && this.productForm.size_type_id != null) {
        const sizes = this.sizes.filter(o => (o.size_type_id == this.productForm.size_type_id && o.numeric == this.sizeNumeric))
        if (this.productForm.sizes.length == 0 && this.edit == false) {
          this.productForm.sizes = sizes.map(o => o.name)
        }
        return sizes
      } else {
        return []
      }
    }
  },
  methods: {
    addColor(param) {
      this.colors.push(param)
      this.productForm.colors.push(param.name)
    },
    addSize(param) {
      this.sizes.push(param)
      this.productForm.sizes.push(param.name)
    },
    showDialog(product = null, readOnly = false) {
      this.readOnly = readOnly
      if (product) {
        this.edit = true
        this.productForm = {
          ...product
        }
      } else {
        this.edit = false
        this.productForm = {
          id: null,
          name: null,
          active: true,
          category_name: null,
          brand_name: null,
          sizes: [],
          colors: [],
          size_type_id: null,
          gender_id: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.productObserver.reset()
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
    async submit() {
      try {
        let valid = await this.$refs.productObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          if (this.edit) {
            const response = await axios.patch(`product/${this.productForm.id}`, this.productForm)
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('product', this.productForm)
            this.$toast.success(response.data.message)
          }
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
