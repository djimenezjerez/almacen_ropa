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
            <v-col cols="12" class="pt-3">
              <v-autocomplete
                label="Producto"
                item-text="name"
                item-value="id"
                :hint="category"
                persistent-hint
                :items="productNames"
                prepend-icon="mdi-tshirt-crew"
                dense
                v-model="productNameId"
                @change="fetchProducts"
              ></v-autocomplete>
            </v-col>
          </v-row>
          <v-row dense v-if="productNameId != null">
            <v-col cols="12">
              <v-select
                :items="sizeTypes"
                item-text="name"
                item-value="id"
                v-model="sizeTypeId"
                label="Tipo de talla"
                prepend-icon="mdi-hanger"
                hide-details
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-select
                :items="genders"
                item-text="name"
                item-value="id"
                v-model="genderId"
                label="Género"
                prepend-icon="mdi-gender-male-female"
                hide-details
              ></v-select>
            </v-col>
            <v-col cols="12">
              <v-data-table
                class="pt-3"
                group-by="brand_name"
                :headers="headers"
                :items="products"
                :options.sync="options"
                :footer-props="{
                  itemsPerPageOptions: [8, 15, 30]
                }"
                :calculate-widths="true"
                v-model="selectedProducts"
                :single-select="false"
                show-select
                dense
              >
                <template v-slot:[`group.header`]="{items, isOpen, toggle}">
                  <th colspan="3">
                    <v-icon @click="toggle"
                      >{{ isOpen ? 'mdi-minus' : 'mdi-plus' }}
                    </v-icon>
                    {{ items[0].brand_name }}
                  </th>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-row dense justify="end">
            <v-col cols="12" md="6">
              <v-btn
                block
                color="info"
                :disabled="selectedProducts.length == 0"
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
  data: function() {
    return {
      dialog: false,
      productNameId: null,
      productNames: [],
      sizeTypeId: null,
      sizeTypes: [],
      genderId: null,
      selectedProducts: [],
      options: {
        page: 1,
        itemsPerPage: 8,
      },
      headers: [
        {
          text: 'MARCA',
          groupable: true,
          sortable: false,
          value: 'brand_name',
        }, {
          text: 'TALLA',
          align: 'center',
          sortable: true,
          value: 'size_name',
        }, {
          text: 'COLOR',
          align: 'center',
          sortable: true,
          value: 'color_name',
        },
      ],
    }
  },
  watch: {
    sizeTypeId() {
      this.genderId = null
      this.selectedProducts = []
    },
    genderId() {
      this.selectedProducts = []
    },
  },
  computed: {
    category() {
      if (this.productNameId != null) {
        return this.productNames.find(o => o.id == this.productNameId).category_name
      } else {
        return ''
      }
    },
    genders() {
      if (this.productNameId != null && this.sizeTypeId != null) {
        return this.sizeTypes.find(o => o.id == this.sizeTypeId).genders
      } else {
        return []
      }
    },
    products() {
      if (this.productNameId != null && this.sizeTypeId != null && this.genderId != null) {
        return this.sizeTypes.find(o => o.id == this.sizeTypeId).genders.find(o => o.id == this.genderId).products
      } else {
        return []
      }
    },
  },
  methods: {
    showDialog() {
      this.dialog = true
      this.productNameId = null
      this.clearSelection()
      this.fetchProductNames()
    },
    selectProducts() {
      const productName = this.productNames.find(o => o.id == this.productNameId)
      const sizeType = this.sizeTypes.find(o => o.id == this.sizeTypeId)
      const gender = this.genders.find(o => o.id == this.genderId)
      this.$emit('updateList', {
        product_name_id: productName.id,
        product_name: productName.name,
        category_id: productName.category_id,
        category_name: productName.category_name,
        size_type_id: sizeType.id,
        size_type_name: sizeType.name,
        gender_id: gender.id,
        gender_name: gender.name,
        products: this.selectedProducts.map(o => {
          o.stock = 1
          o.quantity = 1
          return o
        })
      })
      this.dialog = false
    },
    clearSelection() {
      this.sizeTypeId = null
      this.genderId = null
      this.selectedProducts = []
    },
    async fetchProductNames() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('product', {
          params: {
            combo: true,
          },
        })
        this.productNames = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async fetchProducts() {
      try {
        this.clearSelection()
        this.$store.dispatch('loading', true)
        let response = await axios.get(`product/${this.productNameId}`)
        this.sizeTypes = response.data.product.size_types
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
  },
}
</script>
