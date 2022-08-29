<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="400"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title :title="`Nuevo color`"/>
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
        <validation-observer ref="formObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="name"
                    rules="required|min:1"
                  >
                    <v-text-field
                      label="Color"
                      v-model="form.name"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-tshirt-crew-outline"
                    ></v-text-field>
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
                  >
                    Aceptar
                  </v-btn>
                </v-col>
                <v-col cols="12" md="6">
                  <v-btn
                    block
                    color="error"
                    @click.stop="dialog = false"
                  >
                    Cancelar
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
  name: 'ColorForm',
  data: function() {
    return {
      dialog: false,
      form: {
        name: null,
      },
    }
  },
  methods: {
    showDialog() {
      this.form = {
        name: null,
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.formObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.formObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          const response = await axios.post('color', this.form)
          this.$toast.success(response.data.message)
          this.$emit('updateColors', response.data.color)
          this.dialog = false
        }
      } catch(error) {
        this.$refs.formObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.formObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
