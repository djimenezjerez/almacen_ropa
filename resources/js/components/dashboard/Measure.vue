<template>
  <v-card
    :color="color"
    class="rounded-xl py-6 my-3"
    elevation="8"
    :to="link"
    dark
  >
    <v-row align="center" justify="center" dense>
      <v-col cols="6" class="text-right px-3">
        <v-icon size="38">{{ icon }}</v-icon>
      </v-col>
      <v-col cols="6" class="text-left px-3">
        <div class="text-h4 mx-1">{{ total }}</div>
      </v-col>
      <v-col cols="12" class="text-center">
        <div class="text-h5">{{ name }}</div>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
export default {
  name: 'Measure',
  props: {
    color: {
      type: String,
      required: true
    },
    icon: {
      type: String,
      required: true
    },
    total: {
      type: Number,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    link: {
      type: String,
      required: true
    },
  }
}
</script>
