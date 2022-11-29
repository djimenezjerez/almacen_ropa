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
        <tool-bar-title title="Seleccione el tipo de movimiento"/>
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
              v-for="movementType in movementTypes"
              :key="movementType.id"
              :to="{ path: `/movements/${movementType.code}` }"
              link
            >
              <v-list-item-icon>
                <v-icon v-text="movementType.icon"></v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                {{ movementType.name }}
              </v-list-item-content>
            </v-list-item>
          </v-list-item-group>
        </v-list>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'MovementSelection',
  data: function() {
    return {
      dialog: false,
      movementTypes: [],
    }
  },
  created() {
    this.fetchMovementTypes()
  },
  methods: {
    showDialog() {
      this.dialog = true
    },
    async fetchMovementTypes() {
      try {
        let response = await axios.get(`movement_type`)
        this.movementTypes = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
  }
}
</script>
