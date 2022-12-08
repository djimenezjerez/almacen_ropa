<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Ventas"/>
      </v-toolbar>
      <v-row class="pt-5 px-4">
        <v-col
          cols="12"
          sm="6"
          md="4"
          lg="2"
        >
          <v-menu
            v-model="menuDateFrom"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="computedDateFrom"
                label="Fecha inicial"
                prepend-icon="mdi-calendar"
                dense
                hide-details
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="dateFrom"
              no-title
              @input="menuDateFrom = false"
              @change="fetchMovements"
              :max="$moment().format('YYYY-MM-DD')"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-col
          cols="12"
          sm="6"
          md="4"
          lg="2"
        >
          <v-menu
            v-model="menuDateTo"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="computedDateTo"
                label="Fecha final"
                prepend-icon="mdi-calendar"
                dense
                hide-details
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="dateTo"
              no-title
              @input="menuDateTo = false"
              @change="fetchMovements"
              :max="$moment().format('YYYY-MM-DD')"
            ></v-date-picker>
          </v-menu>
        </v-col>
        <v-col
          cols="12"
          md="4"
          lg="3"
          offset-lg="5"
          xl="2"
          offset-xl="6"
        >
          <add-button
            text="Realizar venta"
            :block="true"
            @click="gotoSell()"
          />
        </v-col>
        <v-col cols="12">
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
          <template v-slot:[`item.total_price`]="{ item }">
            {{ item.total_price.toFixed(2) }}
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
      menuDateFrom: false,
      dateFrom: this.$moment().startOf('month').format('YYYY-MM-DD'),
      menuDateTo: false,
      dateTo: this.$moment().format('YYYY-MM-DD'),
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
          text: 'TOTAL',
          align: 'center',
          sortable: true,
          value: 'total_price',
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
  computed: {
    computedDateFrom () {
      return this.$moment(this.dateFrom).format('DD/MM/YYYY')
    },
    computedDateTo () {
      return this.$moment(this.dateTo).format('DD/MM/YYYY')
    },
  },
  mounted() {
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
            date_from: this.dateFrom,
            date_to: this.dateTo,
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
