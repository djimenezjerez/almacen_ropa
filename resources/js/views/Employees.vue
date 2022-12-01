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
      <building-details :building="store"/>
      <v-row
        class="px-4"
        align="center"
        justify="start"
        dense
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
            text="Agregar empleado"
            :block="$vuetify.breakpoint.smAndDown"
            @click="$refs.employeeForm.showDialog()"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="employees"
          :options.sync="options"
          :server-items-length="totalItems"
          :footer-props="{
            itemsPerPageOptions: [8, 15, 30]
          }"
          :calculate-widths="true"
        >
          <template v-slot:[`item.id`]="{ index }">
            {{ $helpers.listIndex(index, options) }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="3">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      @click="$refs.employeeForm.showDialog(item, true)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-eye
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Ver</span>
                </v-tooltip>
              </v-col>
              <v-col cols="3">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="info"
                      @click="$refs.employeeForm.showDialog(item)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-pencil
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Editar</span>
                </v-tooltip>
              </v-col>
              <v-col cols="3">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="error"
                      @click="$refs.employeeRemove.showDialog(item)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-close-circle
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Remover</span>
                </v-tooltip>
              </v-col>
            </v-row>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
    <employee-form ref="employeeForm" :users="users" :employees="employees.map(i => i.user_id)" :store="store" v-on:updateList="fetchEmployees"/>
    <employee-remove ref="employeeRemove" v-on:updateList="fetchEmployees"/>
  </v-container>
</template>

<script>
export default {
  name: 'Employees',
  components: {
    'employee-form': () => import('@/components/employees/EmployeeForm.vue'),
    'employee-remove': () => import('@/components/employees/EmployeeRemove.vue'),
    'building-details': () => import('@/components/shared/BuildingDetails.vue'),
  },
  data() {
    return {
      breadcrumbs: [
        {
          text: this.$route.params.storeType == 'stores' ? 'Tiendas' : 'Almacenes',
          disabled: false,
          to: {
            path: `/${this.$route.params.storeType}`,
          },
        }, {
          text: 'Empleados',
          disabled: true,
          to: {
            path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/employees`,
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
      store: {},
      employees: [],
      users: [],
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
          width: '120px',
          class: this.$headerClass,
        },
      ],
    }
  },
  mounted() {
    this.fetchUsers()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchEmployees()
      }
    },
    search: function() {
      this.options.page = 1
      this.fetchEmployees()
    }
  },
  methods: {
    async fetchUsers() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('user', {
          params: {
            combo: true,
          }
        })
        this.users = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchStore()
      }
    },
    async fetchStore() {
      try {
        let response = await axios.get(`store/${this.$route.params.storeId}`)
        this.store = response.data.payload.store
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchEmployees()
      }
    },
    async fetchEmployees() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`store/${this.$route.params.storeId}/employee`, {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.employees = response.data.payload.data
        this.totalItems = response.data.payload.total
        this.options.page = response.data.payload.current_page
        this.options.itemsPerPage = parseInt(response.data.payload.per_page)
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
