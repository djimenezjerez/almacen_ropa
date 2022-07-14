<template>
  <v-toolbar-title flat dense class="white--text">{{ title }}</v-toolbar-title>
</template>

<script>
export default {
  name: 'ToolBarTitle',
  props: {
    title: {
      type: String,
      required: true,
    },
  },
}
</script>