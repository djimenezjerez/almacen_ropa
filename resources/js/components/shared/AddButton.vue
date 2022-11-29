<template>
  <v-btn
    :color="color"
    :block="block"
    :disabled="disabled"
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
    disabled: {
      type: Boolean,
      default: false,
    },
  }
}
</script>
