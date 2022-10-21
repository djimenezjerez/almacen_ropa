<template>
  <v-app>
    <v-main>
      <v-container fluid fill-height class="primary">
        <v-row align="center" justify="center" style="">
          <v-col cols="10" md="4">
            <v-card
              class="white rounded-lg"
              elevation="15"

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
                      name="username"
                      rules="required|min:3"
                    >
                      <v-text-field
                        label="Usuario"
                        v-model="loginForm.username"
                        data-vv-name="username"
                        :error-messages="errors"
                        prepend-icon="mdi-account"
                        autofocus
                        :disabled="stores.length > 0"
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
                        :disabled="stores.length > 0"
                      ></v-text-field>
                    </validation-provider>
                    <validation-provider
                      v-if="stores.length > 0"
                      v-slot="{ errors }"
                      name="store_id"
                      rules="required|integer"
                    >
                      <v-select
                        :items="stores"
                        item-text="store_name"
                        item-value="store_id"
                        label="Tienda"
                        v-model="loginForm.store_id"
                        data-vv-name="store_id"
                        :error-messages="errors"
                        prepend-icon="mdi-store"
                        persistent-hint
                        :hint="roleName"
                        ref="storeSelect"
                      ></v-select>
                    </validation-provider>
                  </v-card-text>
                  <v-divider></v-divider>
                  <v-card-actions>
                    <v-btn
                      block
                      type="submit"
                      color="info"
                      :disabled="invalid"
                    >
                      {{ loginForm.store_id != null ? 'Ingresar' : 'Enviar' }}
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
        username: null,
        password: '',
        store_id: null,
      },
      stores: [],
    }
  },
  computed: {
    roleName: function() {
      if (this.loginForm.store_id != null) {
        return `ROL: ${this.stores.find(o => o.store_id == this.loginForm.store_id).role_name}`
      } else {
        return ''
      }
    }
  },
  methods: {
    async fetchStores() {
      try {
        let response = await axios.get('store', {
          params: {
            combo: true,
          }
        })
        this.stores = response.data.payload.stores
      } catch(error) {
        console.error(error)
      }
    },
    async submit() {
      try {
        let valid = await this.$refs.loginObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          await axios.get('sanctum/csrf-cookie')
          if (this.loginForm.store_id != null) {
            await this.$store.dispatch('login', this.loginForm)
            this.$router.push({
              name: 'dashboard'
            })
          } else {
            let response = await axios.post(`login`, this.loginForm)
            this.loginForm.store_id = response.data.payload.stores[0].store_id
            if (response.data.payload.stores.length == 1) {
              this.submit()
            } else {
              this.shadowPassword = true
              this.stores = response.data.payload.stores
              this.$nextTick(() => {
                const input = this.$refs.storeSelect.$el.querySelector('input')
                input.focus()
              })
            }
          }
        }
      } catch(error) {
        this.loginForm.store_id = null
        this.stores = []
        this.$refs.loginObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.loginObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
  },
}
</script>
