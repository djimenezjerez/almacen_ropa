<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-light" :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none;" class="white--text text-h6 font-weight-regular" :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
      </v-toolbar>
      <v-row
        class="background pb-0 pt-2 px-4 mx-0"
        align="center"
        justify="start"
        dense
      >
        <v-col cols="4" md="2">
          <div class="text-right">Tienda: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ building.name }}</div>
        </v-col>
        <v-col cols="4" md="2" v-if="$route.query.building_type == 'store'">
          <div class="text-right">NIT: </div>
        </v-col>
        <v-col cols="4" md="2" v-else>
          <div class="text-right">Responsable: </div>
        </v-col>
        <v-col cols="8" md="4" v-if="$route.query.building_type == 'store'">
          <div class="font-weight-bold">{{ building.document }}</div>
        </v-col>
        <v-col cols="8" md="4" v-else>
          <div class="font-weight-bold">{{ building.user_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Dirección: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ building.address }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Ciudad: </div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ building.city_name }}</div>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
</template>

<script>
export default {
  name: 'Inventory',
  data() {
    return {
      breadcrumbs: [
        {
          text: 'Tiendas',
          disabled: false,
          to: {
            path: '/stores',
          },
        }, {
          text: 'Inventario',
          disabled: true,
          to: {
            path: '/inventory',
            query: {
              building_id: this.$route.query.building_id,
              building_type: this.$route.query.building_type,
            },
          },
        },
      ],
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['people.name'],
        sortDesc: [false]
      },
      totalItems: 0,
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'id',
          class: this.$headerClass,
        }, {
          text: 'NOMBRE',
          align: 'center',
          sortable: true,
          value: 'person_name',
          class: this.$headerClass,
        }, {
          text: 'ROL',
          align: 'center',
          sortable: true,
          value: 'role_display_name',
          class: this.$headerClass,
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '7%',
          class: this.$headerClass,
        },
      ],
      building: {},
    }
  },
  created() {
    this.fetchBuilding()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchProducts()
      }
    },
    search: function() {
      this.options.page = 1
      this.fetchProducts()
    }
  },
  methods: {
    async fetchBuilding() {
      try {
        let response = await axios.get(`${this.$route.query.building_type}/${this.$route.query.building_id}`)
        this.building = response.data.payload[this.$route.query.building_type]
      } catch(error) {
        console.error(error)
      }
    },
    async fetchProducts() {
      console.log('fetchProducts');
    }
  },
}
</script>
