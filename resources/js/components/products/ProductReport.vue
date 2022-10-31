<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title title="Reporte de stock por colores"/>
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
      <div class="px-5 pb-5">

        <v-row dense justify="end">
          <v-col cols="12" md="6">
            <v-btn
              block
              color="error"
              @click.stop="dialog = false"
            >
              Cerrar
            </v-btn>
          </v-col>
        </v-row>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'ProductReport',
  props: {
    sizeType: {
      type: Object,
      required: true
    },
  },
  data: function() {
    return {
      dialog: false,
      
    }
  },
  methods: {
    showDialog() {
      this.dialog = true
    },
  },
}
</script>
