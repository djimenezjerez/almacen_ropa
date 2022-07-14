<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card
      :disabled="loading"
      class="rounded-lg"
    >
      <template slot="progress">
        <v-progress-linear
          color="tertiary"
          height="10"
          indeterminate
        ></v-progress-linear>
      </template>
      <div class="px-5 pb-5">
        <v-card-text>
          <div class="text-center text-lg-h6 text-md-subtitle-1 text-sm-subtitle-2 text-body-1">
            <span>
              ¿Seguro que desea eliminar {{ female ? 'la' : 'el' }} {{ type }}
            </span>
            <span v-if="item.name != null">
              : {{ item.name }}?
            </span>
            <span v-else>?</span>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-row justify="end">
            <v-col cols="auto">
              <v-btn
                color="success darken-1"
                @click="removeItem"
                :disabled="loading"
              >
                <v-icon left>
                  mdi-check
                </v-icon>
                SI
              </v-btn>
            </v-col>
            <v-col cols="auto">
              <v-btn
                color="error"
                @click="dialog = false"
                :disabled="loading"
              >
                <v-icon left>
                  mdi-close
                </v-icon>
                NO
              </v-btn>
            </v-col>
          </v-row>
        </v-card-actions>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'DialogRemoveItem',
  props: {
    female: {
      type: Boolean,
      required: false,
      default: false,
    },
    type: {
      type: String,
      required: true,
    },
    url: {
      type: String,
      required: true,
    }
  },
  data: function() {
    return {
      dialog: false,
      loading: false,
      item: {},
    }
  },
  methods: {
    showDialog(item) {
      this.dialog = true
      this.item = item
    },
    async removeItem() {
      try {
        this.loading = true
        const response = await axios.delete(`${this.url}/${this.item.id}`)
        this.$toast.success(response.data.message)
        this.$emit('updateList')
        this.dialog = false
      } catch(error) {
        console.error(error)
      } finally {
        this.loading = false
      }
    }
  },
}
</script>
