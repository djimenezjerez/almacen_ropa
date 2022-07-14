<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Usuarios"/>
      </v-toolbar>
      <v-row
        class="pt-4 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
          md="8"
          order="last"
          order-md="first"
        >
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
          />
        </v-col>
        <v-col
          cols="12"
          md="4"
          :class="{
            'text-right': $vuetify.breakpoint.mdAndUp,
          }"
          order="first"
          order-md="last"
        >
          <add-button
            :disabled="loading"
            text="Agregar usuario"
            :block="$vuetify.breakpoint.smAndDown"
            @click="$refs.userForm.showDialog()"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="users"
          :options.sync="options"
          :server-items-length="totalItems"
          :disabled="loading"
          :footer-props="{
            itemsPerPageOptions: [8, 15, 30]
          }"
          :calculate-widths="true"
          dense
        >
          <template v-slot:[`item.id`]="{ index }">
            {{ $helpers.listIndex(index, options) }}
          </template>
          <template v-slot:[`item.deleted_at`]="{ item }">
            {{ isActive(item.deleted_at) ? 'ACTIVO' : 'INACTIVO' }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="6" v-if="isActive(item.deleted_at)">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      :disabled="loading"
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="info"
                      @click="$refs.userForm.showDialog(item)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-pencil
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Editar Usuario</span>
                </v-tooltip>
              </v-col>
              <v-col cols="6">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      :disabled="loading"
                      icon
                      v-bind="attrs"
                      v-on="on"
                      :color="isActive(item.deleted_at) ? 'error' : 'success'"
                      :dark="isActive(item.deleted_at)"
                      @click="$refs.userSwitch.showDialog(item)"
                    >
                      <v-icon
                        dense
                      >
                        {{ isActive(item.deleted_at) ? 'mdi-close-circle' : 'mdi-restore' }}
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>{{ isActive(item.deleted_at) ? 'Desactivar usuario' : 'Reactivar usuario' }}</span>
                </v-tooltip>
              </v-col>
            </v-row>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <user-form ref="userForm" :cities="cities" :roles="roles" v-on:updateList="fetchUsers"/>
    <user-switch ref="userSwitch" v-on:updateList="fetchUsers"/>
  </v-container>
</template>

<script>
export default {
  name: 'Users',
  components: {
    'user-form': () => import('@/components/users/UserForm.vue'),
    'user-switch': () => import('@/components/users/UserSwitch.vue'),
  },
  data() {
    return {
      loading: false,
      search: null,
      cities: [],
      roles: [],
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['first_name'],
        sortDesc: [false]
      },
      totalItems: 0,
      users: [],
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'id',
        }, {
          text: 'NOMBRE',
          align: 'center',
          sortable: true,
          value: 'first_name',
        }, {
          text: 'APELLIDO',
          align: 'center',
          sortable: true,
          value: 'last_name',
        }, {
          text: 'CÉDULA DE IDENTIDAD',
          align: 'center',
          sortable: true,
          value: 'identity_card',
        }, {
          text: 'EXPEDICIÓN',
          align: 'center',
          sortable: false,
          value: 'city_code',
        }, {
          text: 'ROL',
          align: 'center',
          sortable: false,
          value: 'role_name',
        }, {
          text: 'TELÉFONO',
          align: 'center',
          sortable: true,
          value: 'phone',
        }, {
          text: 'EMAIL',
          align: 'center',
          sortable: true,
          value: 'email',
        }, {
          text: 'ESTADO',
          align: 'center',
          sortable: true,
          value: 'deleted_at',
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '7%',
        },
      ],
    }
  },
  mounted() {
    if (!this.$vuetify.breakpoint.xs) {
      const table = document.getElementById('datatable').getElementsByTagName('table')[0]
      table.setAttribute('class', 'datatables')
      table.setAttribute('width', '100%')
    }
  },
  created() {
    this.fetchUsers()
    this.fetchCities()
    this.fetchRoles()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchUsers()
      }
    },
    search: function() {
      this.fetchUsers()
    }
  },
  methods: {
    isActive(deleted) {
      return deleted == null
    },
    async fetchRoles() {
      try {
        let response = await axios.get('role', {
          params: {
            combo: true,
          }
        })
        this.roles = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchCities() {
      try {
        let response = await axios.get('city', {
          params: {
            combo: true,
          }
        })
        this.cities = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async fetchUsers() {
      try {
        this.loading = true
        let response = await axios.get('user', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.users = response.data.payload.data
        this.totalItems = response.data.payload.total
        this.options.page = response.data.payload.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.per_page)
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>
