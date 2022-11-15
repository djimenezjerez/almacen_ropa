<template>
  <v-row
    class="background pb-0 pt-2 px-4 mx-0"
    align="center"
    justify="start"
    dense
  >
    <v-col cols="4" md="2">
      <div class="text-right">Tienda: </div>
    </v-col>
    <v-col cols="8" md="4">
      <div class="font-weight-bold">{{ building.name }}</div>
    </v-col>
    <v-col cols="4" md="2">
      <div class="text-right">NIT: </div>
    </v-col>
    <v-col cols="8" md="4">
      <div class="font-weight-bold">{{ building.document }}</div>
    </v-col>
    <v-col cols="4" md="2">
      <div class="text-right">Dirección: </div>
    </v-col>
    <v-col cols="8" md="4">
      <div class="font-weight-bold">{{ building.address }}</div>
    </v-col>
    <v-col cols="4" md="2">
      <div class="text-right">Ciudad: </div>
    </v-col>
    <v-col cols="8" md="4">
      <div class="font-weight-bold">{{ building.city_name }}</div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'BuildingDetails',
  props: {
    building: {
      type: Object,
      required: false,
      default: {
        name: '',
        document: '',
        address: '',
        city_name: '',
      },
    },
  },
}
</script>

