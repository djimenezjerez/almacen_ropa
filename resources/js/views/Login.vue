<template>
  <v-app>
    <v-main>
      <v-container fluid fill-height class="primary">
        <v-row align="center" justify="center" style="">
          <v-col cols="10" md="4">
            <v-card
              class="white rounded-lg"
              elevation="15"
              :disabled="loading"
            >
              <template slot="progress">
                <v-progress-linear
                  color="tertiary"
                  height="10"
                  indeterminate
                ></v-progress-linear>
              </template>
              <div class="py-5">
                <v-img
                  height="200"
                  contain
                  src="/img/logo.png"
                ></v-img>
              </div>
              <v-container class="secondary">
                <v-row class="mx-auto" justify="center" align="center">
                  <v-col cols="12" class="white--text text-center">
                    <div class="white--text text-center text-h5 font-weight-normal">
                      Gestión de Almacén de Ropa
                    </div>
                  </v-col>
                </v-row>
              </v-container>
              <validation-observer ref="loginObserver" v-slot="{ invalid }">
                <form v-on:submit.prevent="submit">
                  <v-card-text class="grey lighten-4">
                    <validation-provider
                      v-slot="{ errors }"
                      name="identity_card"
                      rules="required|min:3"
                    >
                      <v-text-field
                        label="Cédula de Identidad"
                        v-model="loginForm.identity_card"
                        data-vv-name="identity_card"
                        :error-messages="errors"
                        prepend-icon="mdi-account"
                        autofocus
                      ></v-text-field>
                    </validation-provider>
                    <validation-provider
                      v-slot="{ errors }"
                      name="password"
                      rules="required|min:3"
                    >
                      <v-text-field
                        label="Contraseña"
                        v-model="loginForm.password"
                        data-vv-name="password"
                        :error-messages="errors"
                        prepend-icon="mdi-lock"
                        :append-icon="shadowPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        @click:append="() => (shadowPassword = !shadowPassword)"
                        :type="shadowPassword ? 'password' : 'text'"
                      ></v-text-field>
                    </validation-provider>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <v-btn
                      block
                      type="submit"
                      color="info"
                      :disabled="invalid || loading"
                    >
                      Ingresar
                    </v-btn>
                  </v-card-actions>
                </form>
              </validation-observer>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'Login',
  data: function() {
    return {
      shadowPassword: true,
      loginForm: {
        identity_card: null,
        password: '',
      },
      loading: false,
    }
  },
  methods: {
    async submit() {
      try {
        let valid = await this.$refs.loginObserver.validate()
        if (valid) {
          this.loading = true
          await axios.get('sanctum/csrf-cookie')
          await this.$store.dispatch('login', this.loginForm)
          this.$router.push({
            name: 'dashboard'
          })
        }
      } catch(error) {
        this.loginForm.password = ''
        this.$refs.loginObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.loginObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.loading = false
      }
    },
  },
}
</script>