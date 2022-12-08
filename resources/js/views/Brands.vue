<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Configuración de Marcas"/>
      </v-toolbar>
      <v-row
        class="pt-4 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
        >
          <search-input
            v-model="search"
            label="Texto o parámetro de búsqueda"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="brands"
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
              <v-col cols="12">
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
    <dialog-remove ref="dialogRemove" type="marca" url="brand" v-on:updateList="fetchBrands"/>
  </v-container>
</template>

<script>
export default {
  name: 'Brands',
  data() {
    return {
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['name'],
        sortDesc: [false]
      },
      totalItems: 0,
      brands: [],
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
          value: 'name',
          class: this.$headerClass,
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '40px',
          class: this.$headerClass,
        },
      ],
    }
  },
  mounted() {
    this.fetchBrands()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchBrands()
      }
    },
    search: function() {
      this.options.page = 1
      this.fetchBrands()
    }
  },
  methods: {
    isActive(active) {
      return active == true
    },
    async fetchBrands() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('brand', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.brands = response.data.payload.data
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
