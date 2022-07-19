<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card

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
            <div>
              ¿Seguro que desea {{ user.deleted_at == null ? 'remover' : 'reactivar' }} al usuario:
            </div>
            <div>
              {{ [user.first_name, user.last_name].join(' ') }}?
            </div>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-row justify="end">
            <v-col cols="auto">
              <v-btn
                color="success darken-1"
                @click="switchStatus"

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
  name: 'UserSwitch',
  data: function() {
    return {
      dialog: false,
      user: {},
    }
  },
  methods: {
    showDialog(user) {
      this.dialog = true
      this.user = user
    },
    async switchStatus() {
      try {
        this.$store.dispatch('loading', true)
        if (this.user.deleted_at == null) {
          const response = await axios.delete(`user/${this.user.id}`)
          this.$toast.success(response.data.message)
        } else {
          const response = await axios.patch(`deleted/user/${this.user.id}`)
          this.$toast.success(response.data.message)
        }
        this.$emit('updateList')
        this.dialog = false
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
