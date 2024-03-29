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
        <tool-bar-title :title="readOnly ? 'Datos del cliente' : (edit ? 'Editar cliente' : 'Agregar cliente')"/>
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
        <validation-observer ref="clientObserver" v-slot="{ invalid }">
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
                      v-model="clientForm.name"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle"
                      autofocus
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="document"
                    rules="required|min:3|alpha_dash"
                  >
                    <v-text-field
                      label="Documento *"
                      v-model="clientForm.document"
                      data-vv-name="document"
                      :error-messages="errors"
                      prepend-icon="mdi-card-account-details"
                      @input="value => clientForm.document = value.toUpperCase()"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="document_type_id"
                    rules="required|integer"
                  >
                    <v-select
                      :items="documentTypes"
                      item-text="code"
                      item-value="id"
                      label="Tipo de documento *"
                      v-model="clientForm.document_type_id"
                      data-vv-name="document_type_id"
                      :error-messages="errors"
                      prepend-icon="mdi-file-document"
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
                      v-model="clientForm.phone"
                      data-vv-name="phone"
                      :error-messages="errors"
                      prepend-icon="mdi-phone"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="email"
                    rules="email"
                  >
                    <v-text-field
                      label="Email"
                      v-model="clientForm.email"
                      data-vv-name="email"
                      :error-messages="errors"
                      prepend-icon="mdi-at"
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
                      v-model="clientForm.address"
                      data-vv-name="address"
                      :error-messages="errors"
                      prepend-icon="mdi-map-marker"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
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
                      v-model="clientForm.city_id"
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
                      v-model="clientForm.active"
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
  name: 'ClientForm',
  props: {
    documentTypes: {
      type: Array,
      required: true
    },
    cities: {
      type: Array,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      clientForm: {
        id: null,
        name: null,
        active: true,
        document: null,
        address: null,
        email: null,
        phone: null,
        city_id: null,
      },
    }
  },
  methods: {
    showDialog(client = null, readOnly = false) {
      this.readOnly = readOnly
      if (client) {
        this.edit = true
        this.clientForm = {
          ...client
        }
      } else {
        this.edit = false
        this.clientForm = {
          id: null,
          name: null,
          active: true,
          document: null,
          address: null,
          email: null,
          phone: null,
          city_id: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.clientObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.clientObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          if (this.edit) {
            const response = await axios.patch(`client/${this.clientForm.id}`, this.clientForm)
            this.$toast.success(response.data.message)
            this.$emit('updateList', response.data.client)
          } else {
            const response = await axios.post('client', this.clientForm)
            this.$toast.success(response.data.message)
            this.$emit('updateList', response.data.client)
          }
          this.dialog = false
        }
      } catch(error) {
        this.$refs.clientObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.clientObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
