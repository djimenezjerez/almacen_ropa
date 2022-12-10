<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="400"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title title="Cambiar a tienda / almacén"/>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div>
        <v-list>
          <v-list-item-group
            color="primary"
          >
            <v-list-item
              v-for="store in stores"
              :key="store.id"
              @click="submit(store.id)"
            >
              <v-list-item-icon>
                <v-icon>{{ store.warehouse ? 'mdi-package-variant' : 'mdi-store' }}</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                {{ store.store_name }}
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
import store from '@/plugins/store'
export default {
  name: 'StoreSelection',
  data: function() {
    return {
      dialog: false,
      stores: [],
    }
  },
  methods: {
    showDialog() {
      this.stores = []
      this.dialog = true
      this.fetchStores()
    },
    async fetchStores() {
      try {
        let response = await axios.get(`auth`)
        this.stores = response.data.payload.data.filter(o => o.id != this.$store.getters.store.id)
      } catch(error) {
        console.error(error)
      }
    },
    async submit(storeId) {
      try {
        this.$store.dispatch('loading', true)
        await this.$store.dispatch('changeStore', {
          store_id: storeId
        })
        if (this.$route.name != 'dashboard') {
          this.$router.push({
            name: 'dashboard'
          })
        } else {
          this.$nextTick(() => {
            this.$router.go()
          })
        }
        this.dialog = false
      } catch(error) {
        this.$toast.error(error.response.data.message)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
  }
}
</script>
