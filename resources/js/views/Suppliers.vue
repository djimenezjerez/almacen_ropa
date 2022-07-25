<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Proveedores"/>
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
            text="Agregar proveedore"
            :block="$vuetify.breakpoint.smAndDown"
            @click="$refs.supplierForm.showDialog()"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="suppliers"
          :options.sync="options"
          :server-items-length="totalItems"
          :footer-props="{
            itemsPerPageOptions: [8, 15, 30]
          }"
          :calculate-widths="true"
          dense
        >
          <template v-slot:[`item.id`]="{ index }">
            {{ $helpers.listIndex(index, options) }}
          </template>
          <template v-slot:[`item.email`]="{ item }">
            {{ item.email || '-' }}
          </template>
          <template v-slot:[`item.phone`]="{ item }">
            {{ item.phone || '-' }}
          </template>
          <template v-slot:[`item.city_name`]="{ item }">
            {{ item.city_name || '-' }}
          </template>
          <template v-slot:[`item.active`]="{ item }">
            <v-chip
              :color="isActive(item.active) ? 'success' : 'error'"
              dark
              small
            >
              {{ isActive(item.active) ? 'ACTIVO' : 'INACTIVO' }}
            </v-chip>
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="4">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      @click="$refs.supplierForm.showDialog(item, true)"
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
              <v-col cols="4">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="info"
                      @click="$refs.supplierForm.showDialog(item)"
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
              <v-col cols="4">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="error"
                      @click="$refs.dialogRemove.showDialog(item)"
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
    <supplier-form ref="supplierForm" :documentTypes="documentTypes" :cities="cities" v-on:updateList="fetchSuppliers"/>
    <dialog-remove ref="dialogRemove" type="proveedore" url="supplier" v-on:updateList="fetchSuppliers"/>
  </v-container>
</template>

<script>
export default {
  name: 'Suppliers',
  components: {
    'supplier-form': () => import('@/components/suppliers/SupplierForm.vue'),
  },
  data() {
    return {
      search: null,
      documentTypes: [],
      cities: [],
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['name'],
        sortDesc: [false]
      },
      totalItems: 0,
      suppliers: [],
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
          value: 'name',
        }, {
          text: 'DOCUMENTO',
          align: 'center',
          sortable: true,
          value: 'document',
        }, {
          text: 'TIPO DE DOCUMENTO',
          align: 'center',
          sortable: true,
          value: 'document_type_code',
        }, {
          text: 'CIUDAD',
          align: 'center',
          sortable: true,
          value: 'city_name',
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
          value: 'active',
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '9%',
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
    this.fetchSuppliers()
    this.fetchDocumentTypes()
    this.fetchCities()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchSuppliers()
      }
    },
    search: function() {
      this.fetchSuppliers()
    }
  },
  methods: {
    isActive(active) {
      return active == true
    },
    async fetchDocumentTypes() {
      try {
        let response = await axios.get('document_type', {
          params: {
            combo: true,
          }
        })
        this.documentTypes = response.data.payload.data
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
    async fetchSuppliers() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('supplier', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.suppliers = response.data.payload.data
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
