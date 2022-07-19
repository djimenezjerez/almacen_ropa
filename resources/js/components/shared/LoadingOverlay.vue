<template>
  <v-overlay :value="$store.getters.loading" :z-index="1000">
    <v-progress-circular
      indeterminate
      size="64"
    ></v-progress-circular>
  </v-overlay>
</template>

<script>
export default {
  name: 'LoadingOverlay',
}
</script>
