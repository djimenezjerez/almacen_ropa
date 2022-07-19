<template>
  <div>
    <v-container>
      <v-row>
        <v-col
          cols="12"
          md="6"
        >
          <v-card >
            <template slot="progress">
              <progress-bar />
            </template>
            <v-toolbar
              color="secondary"
              dark
            >
              <tool-bar-title title="Datos del usuario"/>
            </v-toolbar>
            <v-card-text>
              <v-simple-table>
                <template v-slot:default>
                  <tbody>
                    <tr>
                      <td class="text-right">Nombre: </td>
                      <td class="font-weight-bold">{{ user.name }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Cédula de identidad: </td>
                      <td class="font-weight-bold">{{ user.document }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Expedición: </td>
                      <td class="font-weight-bold">{{ user.city_name }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Teléfono: </td>
                      <td class="font-weight-bold">{{ user.phone }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Email: </td>
                      <td class="font-weight-bold">{{ user.email }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Usuario: </td>
                      <td class="font-weight-bold">{{ user.username }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Tienda: </td>
                      <td class="font-weight-bold">{{ $store.getters.store.name }}</td>
                    </tr>
                    <tr>
                      <td class="text-right">Rol: </td>
                      <td class="font-weight-bold">{{ $store.getters.role.display_name }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col
          cols="12"
          md="6"
        >
          <v-card >
            <template slot="progress">
              <progress-bar />
            </template>
            <v-toolbar
              color="secondary"
              dark
            >
              <tool-bar-title title="Cambiar contraseña"/>
            </v-toolbar>
            <div class="px-5 pb-5">
              <validation-observer ref="passwordObserver" v-slot="{ invalid }">
                <v-form @submit="changePassword" v-on:submit.prevent>
                  <v-card-text>
                    <validation-provider
                      v-slot="{ errors }"
                      name="old_password"
                      rules="required|min:3"
                    >
                      <v-text-field
                        label="Contraseña actual"
                        v-model="passwordForm.old_password"
                        data-vv-name="old_password"
                        :error-messages="errors"
                        prepend-icon="mdi-lock"
                        type="password"
                      ></v-text-field>
                    </validation-provider>
                    <validation-provider
                      v-slot="{ errors }"
                      name="password"
                      rules="required|min:3"
                    >
                      <v-text-field
                        label="Contraseña nueva"
                        v-model="passwordForm.password"
                        data-vv-name="password"
                        :error-messages="errors"
                        prepend-icon="mdi-lock"
                        :append-icon="shadowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="() => (shadowPassword = !shadowPassword)"
                        :type="shadowPassword ? 'password' : 'text'"
                      ></v-text-field>
                    </validation-provider>
                  </v-card-text>
                  <v-card-actions>
                    <v-btn
                      block
                      type="submit"
                      color="info"
                      :disabled="invalid"
                    >Enviar</v-btn>
                  </v-card-actions>
                </v-form>
              </validation-observer>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
export default {
  name: 'Profile',
  data: function() {
    return {
      shadowPassword: true,
      passwordForm: {
        old_password: '',
        password: '',
      },
      user: {},
      role: {},
      store: {},
    }
  },
  created() {
    this.fetchUser()
  },
  methods: {
    async fetchUser() {
      try {
        this.$store.dispatch('loading', true)
        const response = await axios.get(`user/${this.$store.getters.user.id}`)
        this.user = response.data.payload.user
        this.role = response.data.payload.role
        this.store = response.data.payload.store
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async changePassword() {
      try {
        let valid = await this.$refs.passwordObserver.validate()
        if (valid) {
          if (this.passwordForm.old_password == this.passwordForm.password) {
            this.$refs.passwordObserver.setErrors({
              old_password: ['La contraseña nueva debe ser diferente a la actual'],
            })
            this.passwordForm.password = ''
          } else {
            this.$store.dispatch('loading', true)
            const response = await axios.patch(`user/${this.$store.getters.user.id}`, this.passwordForm)
            await this.$store.dispatch('logout')
            this.$store.dispatch('loading', false)
            this.$toast.success(response.data.message)
            this.$router.push({
              name: 'login',
            })
          }
        }
      } catch(error) {
        this.passwordForm.password = ''
        this.$refs.passwordObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.passwordObserver.setErrors(error.response.data.errors)
        }
        this.$store.dispatch('loading', false)
      }
    },
  }
}
</script>

<style lang="css" scoped>
tr:hover {
  background-color: transparent !important;
}
</style>
