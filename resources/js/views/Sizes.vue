<template>
  <v-container>
    <v-card class="pb-2">
      <v-toolbar
        color="secondary"
      >
        <tool-bar-title title="Configuración de Tallas"/>
      </v-toolbar>
      <v-spacer></v-spacer>
      <v-row
        class="pt-4 px-4"
        align="center"
      >
        <v-col
          cols="12"
          md="4"
          lg="3"
        >
          <v-select
            label="Tipo de talla"
            v-model="sizeType"
            item-text="name"
            :items="sizeTypes"
            prepend-icon="mdi-human-male-boy"
            return-object
            dense
            hide-details
            @change="fetchSizes"
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          md="4"
          lg="3"
        >
          <v-select
            label="Estándar de talla"
            v-model="sizeStandard"
            item-text="name"
            item-value="value"
            :items="sizeStandards"
            prepend-icon="mdi-human-male-height-variant"
            :return-object="false"
            dense
            hide-details
            @change="fetchSizes"
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          md="4"
          lg="6"
        >
          <v-row
            align="start"
            justify="end"
          >
            <v-col
              cols="12"
              lg="6"
              xl="5"
            >
              <v-btn
                block
                color="success"
                @click="submit"
              >
                <v-icon
                  class="mr-3"
                >
                  mdi-check
                </v-icon>
                Guardar orden
              </v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
      <v-card-text v-if="sizes.length > 0">
        <v-simple-table>
          <template v-slot:default>
            <thead>
              <tr>
                <th class="text-center" width="10%"></th>
                <th class="text-center" width="20%">
                  POSICIÓN
                </th>
                <th class="text-center" width="50%">
                  TALLA
                </th>
                <th class="text-center" width="20%">
                  ACCIONES
                </th>
              </tr>
            </thead>
            <draggable v-model="sizes" tag="tbody">
              <tr
                v-for="size in sizes"
                :key="size.id"
              >
                <td class="text-center">
                  <v-icon>
                    mdi-arrow-split-horizontal
                  </v-icon>
                </td>
                <td class="text-center">{{ size.order }}</td>
                <td class="text-center">{{ size.name }}</td>
                <td class="text-center">
                  <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      icon
                      v-bind="attrs"
                      v-on="on"
                      color="error"
                      @click="$refs.dialogRemove.showDialog(size)"
                    >
                      <v-icon
                        dense
                      >
                        mdi-close-circle
                      </v-icon>
                    </v-btn>
                  </template>
                  <span>Remover</span>
                </v-tooltip>
                </td>
              </tr>
            </draggable>
          </template>
        </v-simple-table>
      </v-card-text>
    </v-card>
    <dialog-remove ref="dialogRemove" :female="true" type="talla" url="size" v-on:updateList="fetchSizes"/>
  </v-container>
</template>

<script>
import draggable from 'vuedraggable'

export default {
  name: 'Sizes',
  components: {
    draggable
  },
  data() {
    return {
      sizes: [],
      sizeType: {},
      sizeTypes: [],
      sizeStandard: 'numeric',
      sizeStandards: [
        {
          name: 'Tallas numéricas',
          value: 'numeric'
        }, {
          name: 'Tallas alfabéticas',
          value: 'alphabetic'
        },
      ],
    }
  },
  mounted() {
    this.fetchSizeTypes()
  },
  methods: {
    async fetchSizeTypes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get(`size_type`)
        this.sizeTypes = response.data.payload.data
        this.sizeType = this.sizeTypes[0]
      } catch(error) {
        console.error(error)
      } finally {
        this.fetchSizes()
      }
    },
    async fetchSizes() {
      try {
        this.$store.dispatch('loading', true)
        let response = await axios.get('size', {
          params: {
            size_type_id: this.sizeType.id,
            numeric: this.sizeStandard == 'numeric' ? 1 : 0,
          },
        })
        this.sizes = response.data.payload.data
      } catch(error) {
        console.error(error)
      } finally {
        this.$store.dispatch('loading', false)
      }
    },
    async submit() {
      try {
        this.$store.dispatch('loading', true)
        const response = await axios.patch('size', {
          sizes: this.sizes.map((o, i) => ({
            id: o.id,
            order: (i+1),
          }))
        })
        this.$toast.success(response.data.message)
      } catch(error) {
        this.$toast.error(error.response.data.errors[Object.keys(error.response.data.errors)[0]][0])
      } finally {
        this.fetchSizes()
      }
    },
  },
}
</script>
