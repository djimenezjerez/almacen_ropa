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
                    <v-text-field
                      label="Nombre"
                      v-model="productForm.name"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-hanger"
                      autofocus
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
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="color_name"
                    rules="required"
                  >
                    <v-combobox
                      label="Color"
                      v-model="productForm.color_name"
                      item-text="name"
                      item-value="name"
                      :items="colors"
                      data-vv-name="color_name"
                      :error-messages="errors"
                      prepend-icon="mdi-format-color-fill"
                      :return-object="false"
                    ></v-combobox>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="size_name"
                    rules="required"
                  >
                    <v-combobox
                      label="Talla"
                      v-model="productForm.size_name"
                      item-text="name"
                      item-value="name"
                      :items="sizes"
                      data-vv-name="size_name"
                      :error-messages="errors"
                      prepend-icon="mdi-size-xl"
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
                      label="Tipo de talla"
                      v-model="productForm.size_type_id"
                      data-vv-name="size_type_id"
                      :error-messages="errors"
                      prepend-icon="mdi-human-male-girl"
                    ></v-select>
                  </validation-provider>
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
  </v-dialog>
</template>

<script>
export default {
  name: 'productForm',
  props: {
    categories: {
      type: Array,
      required: true
    },
    brands: {
      type: Array,
      required: true
    },
    sizes: {
      type: Array,
      required: true
    },
    sizeTypes: {
      type: Array,
      required: true
    },
    colors: {
      type: Array,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      productForm: {
        id: null,
        name: null,
        active: true,
        category_name: null,
        brand_name: null,
        size_name: null,
        color_name: null,
        size_type_id: null,
      },
    }
  },
  methods: {
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
          size_name: null,
          color_name: null,
          size_type_id: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.productObserver.reset()
      })
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
