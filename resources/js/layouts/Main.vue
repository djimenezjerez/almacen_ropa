<template>
  <v-app>
    <v-app-bar
      app
      dense
      color="tertiary"
      v-if="$store.getters.isLoggedIn"
    >
      <v-app-bar-nav-icon
        @click="drawer = !drawer"
      ></v-app-bar-nav-icon>
      <v-divider class="ml-1 mr-5" vertical></v-divider>
      <v-btn
        outlined
        to="dashboard"
      >
        <v-icon
          class="mr-3"
        >
          mdi-home
        </v-icon>
        Inicio
      </v-btn>
      <v-spacer></v-spacer>
      <v-menu
        bottom
        offset-y
        light
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            dark
            color="info"
            v-bind="attrs"
            v-on="on"
          >
            <v-icon
              class="mr-3"
            >
              mdi-account
            </v-icon>
            {{ $store.getters.user.name }}
          </v-btn>
        </template>
        <v-list>
          <v-list-item @click="() => $router.push({ name: 'profile' })">
            <v-icon color="info">
              mdi-account-outline
            </v-icon>
            <v-list-item-title class="mr-3">PERFIL</v-list-item-title>
          </v-list-item>
          <v-list-item
            @click="logout"
          >
            <v-icon color="error">
              mdi-power
            </v-icon>
            <v-list-item-title class="mr-3">CERRAR SESIÓN</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <!-- Barra lateral de Menu Aplicación -->
    <v-navigation-drawer
      app
      permanent
      dark
      :mini-variant.sync="drawer"
      color="primary"
    >
      <v-row align="center" justify="center" class="white py-3" dense>
        <v-col cols="auto" class="text-center">
          <v-img
            src="/img/logo.png"
            contain
            :width="drawer ? 25 : 180"
          ></v-img>
        </v-col>
      </v-row>
      <v-row align="center" justify="center" class="tertiary" dense v-show="!drawer">
        <v-col cols="12" sm="11" md="10" class="text-center pt-3 pb-0">
          <v-text-field
            light
            :value="$store.getters.store.name"
            label="Tienda"
            dense
            readonly
            hide-details
            outlined
          ></v-text-field>
        </v-col>
        <v-col cols="12" sm="11" md="10" class="text-center pt-2 pb-2">
          <v-text-field
            light
            :value="$store.getters.role.display_name"
            label="Rol"
            dense
            readonly
            hide-details
            outlined
          ></v-text-field>
        </v-col>
      </v-row>
      <v-divider></v-divider>
      <v-list
        nav
        dense
      >
        <!-- Usuarios -->
        <v-list-item link :to="{ name: 'users' }" v-if="$store.getters.user.permissions.includes('USUARIOS')">
          <v-list-item-icon>
            <v-icon>mdi-account</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Usuarios</v-list-item-content>
        </v-list-item>
        <!-- Tiendas -->
        <v-list-item link :to="{ name: 'stores' }" v-if="$store.getters.user.permissions.includes('TIENDAS')">
          <v-list-item-icon>
            <v-icon>mdi-store</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Tiendas</v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main class="blue-grey lighten-5">
      <router-view></router-view>
    </v-main>
    <loading-overlay></loading-overlay>
  </v-app>
</template>

<script>
export default {
  name: 'Main',
  data: function() {
    return {
      drawer: false,
    }
  },
  methods: {
    async logout() {
      try {
        await this.$store.dispatch('logout')
        this.$router.push({
          name: 'login'
        })
      } catch(error) {
        console.error(error)
      }
    }
  }
}
</script>
