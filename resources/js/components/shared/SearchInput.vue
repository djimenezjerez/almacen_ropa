<template>
  <v-text-field
    :label="label"
    prepend-icon="mdi-magnify"
    filled
    outlined
    single-line
    clearable
    dense
    hide-details=""
    v-model="searchValue"
    @input="inputUpdated"
    v-on:keyup.enter="searchValue && $emit('input', searchValue)"
    :class="{ 'mr-4': $vuetify.breakpoint.mdAndUp }"
  ></v-text-field>
</template>

<script>
import _ from 'lodash'

export default {
  name: 'SearchInput',
  props: {
    label: {
      type: String,
      default: 'Buscar',
    },
  },
  data: function() {
    return {
      searchValue: '',
    }
  },
  methods: {
    inputUpdated: _.debounce(function (value) {
        if (value == null || value.length >= 3 || value.length == 0) {
          this.$emit('input', value)
        }
      }, 500
    )
  }
}
</script>
