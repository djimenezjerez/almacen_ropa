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
        <tool-bar-title :title="readOnly ? 'Datos de la categoría' : (edit ? 'Editar categoría' : 'Agregar categoría')"/>
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
        <validation-observer ref="categoryObserver" v-slot="{ invalid }">
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
                      v-model="clientForm.name"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-format-list-bulleted-type"
                      autofocus
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
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      clientForm: {
        id: null,
        name: null,
        active: true,
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
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.categoryObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.categoryObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          if (this.edit) {
            const response = await axios.patch(`category/${this.clientForm.id}`, this.clientForm)
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('category', this.clientForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.categoryObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.categoryObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
