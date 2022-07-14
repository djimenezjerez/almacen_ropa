<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card
      :disabled="loading"
    >
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title :title="edit ? 'Editar usuario' : 'Agregar usuario'"/>
        <v-spacer></v-spacer>
        <v-btn
          :disabled="loading"
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="userObserver" v-slot="{ invalid }">
          <form v-on:submit.prevent="submit">
            <v-card-text>
              <v-row dense>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="first_name"
                    rules="required|min:3|alpha_spaces"
                  >
                    <v-text-field
                      label="Nombre"
                      v-model="userForm.first_name"
                      data-vv-name="first_name"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle"
                      autofocus
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="last_name"
                    rules="required|min:3|alpha_spaces"
                  >
                    <v-text-field
                      label="Apellido"
                      v-model="userForm.last_name"
                      data-vv-name="last_name"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle-outline"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="identity_card"
                    rules="required|min:3|alpha_dash"
                  >
                    <v-text-field
                      label="Documento de Identidad"
                      v-model="userForm.identity_card"
                      data-vv-name="identity_card"
                      :error-messages="errors"
                      prepend-icon="mdi-card-account-details"
                      @input="value => userForm.identity_card = value.toUpperCase()"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="city_id"
                    rules="required"
                  >
                    <v-select
                      :items="cities"
                      item-text="code"
                      item-value="id"
                      label="Expedición"
                      v-model="userForm.city_id"
                      data-vv-name="city_id"
                      :error-messages="errors"
                      prepend-icon="mdi-map-marker"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="role_id"
                    rules="required"
                  >
                    <v-select
                      :items="roles"
                      item-text="name"
                      item-value="id"
                      label="Rol"
                      v-model="userForm.role_id"
                      data-vv-name="role_id"
                      :error-messages="errors"
                      prepend-icon="mdi-key-variant"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="email"
                    rules="required|email"
                  >
                    <v-text-field
                      label="Email"
                      v-model="userForm.email"
                      data-vv-name="email"
                      :error-messages="errors"
                      prepend-icon="mdi-at"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="phone"
                    rules="required|min:7|integer"
                  >
                    <v-text-field
                      label="Teléfono"
                      v-model="userForm.phone"
                      data-vv-name="phone"
                      :error-messages="errors"
                      prepend-icon="mdi-phone"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="password"
                    :rules="edit ? '' : 'required|min:4'"
                    v-if="edit"
                  >
                    <v-text-field
                      label="Contraseña"
                      v-model="userForm.password"
                      data-vv-name="password"
                      :error-messages="errors"
                      prepend-icon="mdi-lock"
                      :append-icon="shadowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      @click:append="() => (shadowPassword = !shadowPassword)"
                      :type="shadowPassword ? 'password' : 'text'"
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
                    :disabled="invalid || loading"
                  >
                    Guardar
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-actions>
          </form>
        </validation-observer>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'UserForm',
  props: {
    cities: {
      type: Array,
      required: true
    },
    roles: {
      type: Array,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      edit: false,
      shadowPassword: true,
      userForm: {
        id: null,
        first_name: '',
        last_name: '',
        identity_card: '',
        password: '',
        email: '',
        phone: '',
        city_id: null,
        role_id: null,
      },
      loading: false,
    }
  },
  methods: {
    showDialog(user = null) {
      this.shadowPassword = true
      if (user) {
        this.edit = true
        this.userForm = {
          ...user
        }
      } else {
        this.edit = false
        this.userForm = {
          id: null,
          first_name: '',
          last_name: '',
          identity_card: '',
          password: '',
          email: '',
          phone: '',
          city_id: null,
          role_id: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.userObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.userObserver.validate()
        if (valid) {
          this.loading = true
          if (this.edit) {
            const response = await axios.patch(`user/${this.userForm.id}`, this.userForm)
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('user', this.userForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.userObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.userObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    }
  },
}
</script>
