<template>
  <v-btn
    :disabled="loading"
    :color="color"
    :block="block"
    @click="$emit('click', $event)"
  >
    <v-icon
      class="mr-3"
    >
      {{ icon }}
    </v-icon>
    <div>
      {{ text }}
    </div>
  </v-btn>
</template>

<script>
export default {
  name: 'AddButton',
  props: {
    icon: {
      type: String,
      default: 'mdi-plus'
    },
    text: {
      type: String,
      default: 'Agregar'
    },
    color: {
      type: String,
      default: 'info'
    },
    block: {
      type: Boolean,
      default: false,
    },
    loading: {
      type: Boolean,
      default: false,
    },
  }
}
</script>
