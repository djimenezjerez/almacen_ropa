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
        <tool-bar-title :title="readOnly ? 'Datos del almacén' : (edit ? 'Editar almacén' : 'Agregar almacén')"/>
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
        <validation-observer ref="warehouseObserver" v-slot="{ invalid }">
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
                      v-model="warehouseForm.name"
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
                      v-model="warehouseForm.address"
                      data-vv-name="address"
                      :error-messages="errors"
                      prepend-icon="mdi-map-marker"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="user_id"
                    rules="numeric"
                  >
                    <v-autocomplete
                      label="Responsable"
                      v-model="warehouseForm.user_id"
                      item-text="name"
                      item-value="id"
                      :items="users"
                      data-vv-name="user_id"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle"
                      clearable
                    ></v-autocomplete>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="city_id"
                    rules="required|integer"
                  >
                    <v-select
                      :items="cities"
                      item-text="name"
                      item-value="id"
                      label="Ciudad *"
                      v-model="warehouseForm.city_id"
                      data-vv-name="city_id"
                      :error-messages="errors"
                      prepend-icon="mdi-map"
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
                      v-model="warehouseForm.active"
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
  name: 'warehouseForm',
  props: {
    cities: {
      type: Array,
      required: true
    },
    users: {
      type: Array,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      warehouseForm: {
        id: null,
        name: null,
        address: null,
        active: true,
        city_id: null,
        user_id: null,
      },
    }
  },
  methods: {
    showDialog(warehouse = null, readOnly = false) {
      this.readOnly = readOnly
      if (warehouse) {
        this.edit = true
        this.warehouseForm = {
          ...warehouse
        }
      } else {
        this.edit = false
        this.warehouseForm = {
          id: null,
          name: null,
          address: null,
          active: true,
          city_id: null,
          user_id: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.warehouseObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.warehouseObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          if (this.edit) {
            const response = await axios.patch(`warehouse/${this.warehouseForm.id}`, this.warehouseForm)
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('warehouse', this.warehouseForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.warehouseObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.warehouseObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
