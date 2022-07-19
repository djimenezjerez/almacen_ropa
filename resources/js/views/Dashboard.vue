<template>
  <v-container>
    <v-row class="mx-auto my-md-4" justify="center" align="center">
      <v-col cols="12" md="5" lg="12">
        <div class="text-center text-h4 font-weight-bold">
          Gestión de Almacén de Ropa
        </div>
      </v-col>
    </v-row>
    <v-row class="mx-auto mt-md-12" justify="center" align="center">
      <v-col cols="12" md="4" lg="3" class="px-5" v-for="measure in measurements" :key="measure.title">
        <measure
          :color="measure.color"
          :icon="measure.icon"
          :total="measure.total"
          :name="measure.title"
          :link="measure.link"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'Buildings',
  components: {
    'measure': () => import('@/components/dashboard/Measure.vue'),
  },
  data() {
    return {
      measurements: [],
    }
  },
  mounted() {
    this.fetchDashboard()
  },
  methods: {
    async fetchDashboard() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('dashboard')
        this.measurements = response.data.payload.data
        this.$nextTick(() => {
          this.$forceUpdate()
        })
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
  },
}
</script>
