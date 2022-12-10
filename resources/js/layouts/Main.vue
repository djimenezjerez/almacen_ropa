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
        to="/dashboard"
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
      stateless
      touchless
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
        <v-col cols="12" sm="11" md="10" class="text-center pt-2 pb-2">
          <v-text-field
            light
            :value="$store.getters.store.name"
            :label="$store.getters.store.warehouse ? 'Almacén' : 'Tienda'"
            dense
            readonly
            hide-details
            outlined
            @click="$refs.storeSelection.showDialog()"
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
        <v-list-item link :to="{ name: 'users' }" v-if="$store.getters.user.permissions.includes('USUARIOS')">
          <v-list-item-icon>
            <v-icon>mdi-account</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Usuarios</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'stores' }" v-if="$store.getters.user.permissions.includes('TIENDAS')">
          <v-list-item-icon>
            <v-icon>mdi-store</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Tiendas</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'warehouses' }" v-if="$store.getters.user.permissions.includes('ALMACENES')">
          <v-list-item-icon>
            <v-icon>mdi-package-variant</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Almacenes</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'suppliers' }" v-if="$store.getters.user.permissions.includes('PROVEEDORES')">
          <v-list-item-icon>
            <v-icon>mdi-van-utility</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Proveedores</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'clients' }" v-if="$store.getters.user.permissions.includes('CLIENTES')">
          <v-list-item-icon>
            <v-icon>mdi-account-cash</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Clientes</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'products' }" v-if="$store.getters.user.permissions.includes('PRODUCTOS')">
          <v-list-item-icon>
            <v-icon>mdi-tshirt-crew</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Productos</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'movements' }" v-if="$store.getters.user.permissions.includes('TRANSFERENCIAS')">
          <v-list-item-icon>
            <v-icon>mdi-swap-horizontal</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Movimientos de stock</v-list-item-content>
        </v-list-item>
        <v-list-item link :to="{ name: 'sells' }" v-if="$store.getters.user.permissions.includes('VENTAS') && !$store.getters.store.warehouse">
          <v-list-item-icon>
            <v-icon>mdi-cash-register</v-icon>
          </v-list-item-icon>
          <v-list-item-content>Ventas</v-list-item-content>
        </v-list-item>
        <v-list-group color="white" v-if="$store.getters.user.permissions.includes('REPORTES')">
          <template v-slot:activator>
            <v-list-item class="px-0">
              <v-list-item-icon>
                <v-icon>mdi-chart-bar</v-icon>
              </v-list-item-icon>
              <v-list-item-content>Reportes</v-list-item-content>
            </v-list-item>
          </template>
          <v-list-item link :to="{ name: 'reportProducts' }" :class="drawer ? 'ml-1' : 'ml-4'">
            <v-list-item-icon>
              <v-icon>mdi-tshirt-v-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-content>Inventario</v-list-item-content>
          </v-list-item>
          <v-list-item link :to="{ name: 'reportSells' }" :class="drawer ? 'ml-1' : 'ml-4'" v-if="!$store.getters.store.warehouse">
            <v-list-item-icon>
              <v-icon>mdi-currency-usd</v-icon>
            </v-list-item-icon>
            <v-list-item-content>Productos vendidos</v-list-item-content>
          </v-list-item>
        </v-list-group>
        <v-list-group color="white" v-if="$store.getters.user.permissions.includes('CONFIGURACION')">
          <template v-slot:activator>
            <v-list-item class="px-0">
              <v-list-item-icon>
                <v-icon>mdi-cog</v-icon>
              </v-list-item-icon>
              <v-list-item-content>Configuración</v-list-item-content>
            </v-list-item>
          </template>
          <v-list-item link :to="{ name: 'categories' }" :class="drawer ? 'ml-1' : 'ml-4'">
            <v-list-item-icon>
              <v-icon>mdi-format-list-bulleted-type</v-icon>
            </v-list-item-icon>
            <v-list-item-content>Categorías</v-list-item-content>
          </v-list-item>
          <v-list-item link :to="{ name: 'sizes' }" :class="drawer ? 'ml-1' : 'ml-4'">
            <v-list-item-icon>
              <v-icon>mdi-human-male-height-variant</v-icon>
            </v-list-item-icon>
            <v-list-item-content>Tallas</v-list-item-content>
          </v-list-item>
          <v-list-item link :to="{ name: 'colors' }" :class="drawer ? 'ml-1' : 'ml-4'">
            <v-list-item-icon>
              <v-icon>mdi-invert-colors</v-icon>
            </v-list-item-icon>
            <v-list-item-content>Colores</v-list-item-content>
          </v-list-item>
          <v-list-item link :to="{ name: 'brands' }" :class="drawer ? 'ml-1' : 'ml-4'">
            <v-list-item-icon>
              <v-icon>mdi-shopping-outline</v-icon>
            </v-list-item-icon>
            <v-list-item-content>Marcas</v-list-item-content>
          </v-list-item>
        </v-list-group>
      </v-list>
    </v-navigation-drawer>
    <v-main class="blue-grey lighten-5">
      <router-view></router-view>
    </v-main>
    <loading-overlay/>
    <store-selection ref="storeSelection"/>
  </v-app>
</template>

<script>
export default {
  name: 'Main',
  components: {
    'store-selection': () => import('@/components/shared/StoreSelection.vue'),
  },
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
          name: 'auth'
        })
      } catch(error) {
        console.error(error)
      }
    }
  }
}
</script>
