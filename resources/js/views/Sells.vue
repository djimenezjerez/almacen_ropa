<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Ventas"/>
      </v-toolbar>
      <v-row
        class="pt-4 px-4"
        align="center"
        justify="start"
      >
        <v-col
          cols="12"
          md="7"
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
          md="5"
          :class="{
            'text-right': $vuetify.breakpoint.mdAndUp,
          }"
          order="first"
          order-md="last"
        >
          <add-button
            text="Realizar venta"
            :block="$vuetify.breakpoint.smAndDown"
            @click="gotoSell()"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="sells"
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
          <template v-slot:[`item.created_at`]="{ item }">
            {{ item.created_at | moment('L') }}
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
                      color="warning"
                      @click="gotoDetails(item.id)"
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
            </v-row>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'Sells',
  data() {
    return {
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: [],
        sortDesc: []
      },
      totalItems: 0,
      sells: [],
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'id',
          class: this.$headerClass,
        }, {
          text: 'FECHA',
          align: 'center',
          sortable: true,
          value: 'created_at',
          class: this.$headerClass,
        }, {
          text: 'USUARIO',
          align: 'center',
          sortable: true,
          value: 'user_name',
          class: this.$headerClass,
        }, {
          text: 'CLIENTE',
          align: 'center',
          sortable: true,
          value: 'client_name',
          class: this.$headerClass,
        }, {
          text: 'TIPO DE DOCUMENTO',
          align: 'center',
          sortable: true,
          value: 'document_type_code',
          class: this.$headerClass,
        }, {
          text: 'DOCUMENTO',
          align: 'center',
          sortable: true,
          value: 'client_document',
          class: this.$headerClass,
        }, {
          text: 'ACCIONES',
          align: 'center',
          value: 'actions',
          sortable: false,
          width: '30px',
          class: this.$headerClass,
        },
      ],
    }
  },
  created() {
    this.fetchMovements()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchMovements()
      }
    },
    search: function() {
      this.options.page = 1
      this.fetchMovements()
    }
  },
  methods: {
    gotoDetails(id) {
      this.$router.push({
        path: `/sells/${id}`,
        query: {
          type: 'sells',
        },
      })
    },
    gotoSell() {
      this.$router.push({
        path: `/sells/new`,
      })
    },
    async fetchMovements() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('movement', {
          params: {
            store_id: this.$store.getters.store.id,
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
            active: 0,
          },
        })
        this.sells = response.data.payload.data
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
