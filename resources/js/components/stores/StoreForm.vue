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
        <tool-bar-title :title="(readOnly ? 'Datos de ' : (edit ? 'Editar ' : 'Agregar ')) + (warehouse ? 'almacén' : 'tienda')"/>
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
        <validation-observer ref="storeObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit" :readonly="readOnly">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="name"
                    rules="required|min:3|alpha_spaces"
                  >
                    <v-text-field
                      label="Nombre *"
                      v-model="storeForm.name"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle"
                      autofocus
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="address"
                    rules="min:3"
                  >
                    <v-text-field
                      label="Direcciòn"
                      v-model="storeForm.address"
                      data-vv-name="address"
                      :error-messages="errors"
                      prepend-icon="mdi-map-marker"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6" v-if="!warehouse">
                  <validation-provider
                    v-slot="{ errors }"
                    name="document"
                    rules="required|min:3|alpha_dash"
                  >
                    <v-text-field
                      label="NIT *"
                      v-model="storeForm.document"
                      data-vv-name="document"
                      :error-messages="errors"
                      prepend-icon="mdi-card-account-details"
                      @input="value => storeForm.document = value.toUpperCase()"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="city_id"
                    rules="integer"
                  >
                    <v-select
                      :items="cities"
                      item-text="name"
                      item-value="id"
                      label="Ciudad"
                      v-model="storeForm.city_id"
                      data-vv-name="city_id"
                      :error-messages="errors"
                      prepend-icon="mdi-map"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="phone"
                    rules="min:7|integer"
                  >
                    <v-text-field
                      label="Teléfono"
                      v-model="storeForm.phone"
                      data-vv-name="phone"
                      :error-messages="errors"
                      prepend-icon="mdi-phone"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6" v-if="!warehouse">
                  <validation-provider
                    v-slot="{ errors }"
                    name="email"
                    rules="email"
                  >
                    <v-text-field
                      label="Email"
                      v-model="storeForm.email"
                      data-vv-name="email"
                      :error-messages="errors"
                      prepend-icon="mdi-at"
                    ></v-text-field>
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
                      v-model="storeForm.active"
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
  </v-dialog>
</template>

<script>
export default {
  name: 'StoreForm',
  props: {
    cities: {
      type: Array,
      required: true
    },
    warehouse: {
      type: Boolean,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      storeForm: {
        id: null,
        name: null,
        active: true,
        document: null,
        address: null,
        email: null,
        phone: null,
        city_id: null,
        warehouse: Number(this.warehouse),
      },
    }
  },
  methods: {
    showDialog(store = null, readOnly = false) {
      this.readOnly = readOnly
      if (store) {
        this.edit = true
        this.storeForm = {
          ...store
        }
      } else {
        this.edit = false
        this.storeForm = {
          id: null,
          name: null,
          active: true,
          document: null,
          address: null,
          email: null,
          phone: null,
          city_id: null,
          warehouse: Number(this.warehouse),
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.storeObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.storeObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          if (this.edit) {
            const response = await axios.patch(`store/${this.storeForm.id}`, this.storeForm)
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('store', this.storeForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.storeObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.storeObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
