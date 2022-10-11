<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Transferencias de stock"/>
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
            text="Agregar transferencia"
            :block="$vuetify.breakpoint.smAndDown"
            @click="gotoStockTransferForm"
          />
        </v-col>
      </v-row>
    </v-card>
    <v-row>
      <v-col cols="12">
        <v-data-table
          id="datatable"
          :headers="headers"
          :items="stockTransfers"
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
          <template v-slot:[`item.updated_at`]="{ item }">
            {{ transferType(item) }}
          </template>
          <template v-slot:[`item.created_at`]="{ item }">
            {{ item.created_at | moment('L') }}
          </template>
          <template v-slot:[`item.origin_store_id`]="{ item }">
            {{ item.origin_store_name || item.origin_warehouse_name || '-' }}
          </template>
          <template v-slot:[`item.destiny_store_id`]="{ item }">
            {{ item.destiny_store_name || item.destiny_warehouse_name || '-' }}
          </template>
          <template v-slot:[`item.actions`]="{ item }">
            <v-row dense no-gutters justify="space-around" align="center">
              <v-col cols="6">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="warning"
                      @click="$refs.stockTransferForm.showDialog(item, true)"
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
              <v-col cols="6">
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
    <dialog-remove ref="dialogRemove" type="transferencia" url="stockTransfer" v-on:updateList="fetchStockTransfers"/>
  </v-container>
</template>

<script>
export default {
  name: 'StockTransfers',
  data() {
    return {
      search: null,
      options: {
        page: 1,
        itemsPerPage: 8,
        sortBy: ['created_at'],
        sortDesc: [true]
      },
      totalItems: 0,
      stockTransfers: [],
      headers: [
        {
          text: 'NRO',
          align: 'center',
          sortable: false,
          value: 'id',
        }, {
          text: 'TIPO',
          align: 'center',
          sortable: true,
          value: 'updated_at',
        }, {
          text: 'DESDE',
          align: 'center',
          sortable: true,
          value: 'origin_store_id',
        }, {
          text: 'HACIA',
          align: 'center',
          sortable: true,
          value: 'destiny_store_id',
        }, {
          text: 'FECHA',
          align: 'center',
          sortable: true,
          value: 'created_at',
        }, {
          text: 'USUARIO',
          align: 'center',
          sortable: true,
          value: 'user_name',
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
    this.fetchStockTransfers()
  },
  watch: {
    options: function(newVal, oldVal) {
      if (newVal.page != oldVal.page || newVal.itemsPerPage != oldVal.itemsPerPage || newVal.sortBy != oldVal.sortBy || newVal.sortDesc != oldVal.sortDesc) {
        this.fetchStockTransfers()
      }
    },
    search: function() {
      this.fetchStockTransfers()
    }
  },
  methods: {
    transferType(item) {
      if (item.origin_store_id == null && item.origin_warehouse_id == null) {
        return 'Ingreso'
      } else if (item.destiny_store_id == null && item.destiny_warehouse_id == null) {
        return 'Ajuste'
      } else {
        return 'Transferencia'
      }
    },
    gotoStockTransferForm() {
      this.$router.push({ path: '/stock_transfer_form' })
    },
    async fetchStockTransfers() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('stock_transfer', {
          params: {
            page: this.options.page,
            per_page: this.options.itemsPerPage,
            sort_by: this.options.sortBy,
            sort_desc: this.options.sortDesc,
            search: this.search,
          },
        })
        this.stockTransfers = response.data.payload.data
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
