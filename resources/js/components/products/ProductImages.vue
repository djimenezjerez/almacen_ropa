<template>
  <v-container>
    <v-card>
      <v-toolbar color="secondary">
        <router-link style="text-decoration: none" class="white--text text-h6 font-weight-light"
          :to="breadcrumbs[0].to">{{ breadcrumbs[0].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none" class="white--text text-h6 font-weight-light"
          :to="breadcrumbs[1].to">{{ breadcrumbs[1].text }}</router-link>
        <span class="white--text px-3">/</span>
        <router-link style="text-decoration: none" class="white--text text-h6 font-weight-regular"
          :to="breadcrumbs[2].to">{{ breadcrumbs[2].text }}</router-link>
        <span class="white--text px-3" v-if="isBuilding">/</span>
        <router-link style="text-decoration: none" class="white--text text-h6 font-weight-regular"
          :to="breadcrumbs[3].to" v-if="isBuilding">{{ breadcrumbs[3].text }}</router-link>
      </v-toolbar>
      <building-details v-if="isBuilding" :building="store" />
      <v-row class="background pb-0 pt-2 px-4 mx-0" align="center" justify="start" dense>
        <v-col cols="4" md="2">
          <div class="text-right">Producto:</div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.product_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Stock:</div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.total_stock }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Categoría:</div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.category_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Tipo de talla:</div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.size_type_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Marca:</div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.brand_name }}</div>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Color:</div>
        </v-col>
        <v-col cols="8" md="4">
          <v-chip :color="product.color_hex" label>{{
            product.color_name
          }}</v-chip>
        </v-col>
        <v-col cols="4" md="2">
          <div class="text-right">Género:</div>
        </v-col>
        <v-col cols="8" md="4">
          <div class="font-weight-bold">{{ product.gender_name }}</div>
        </v-col>
      </v-row>
      <v-row class="px-4 py-0 my-0" align="center" justify="start">
        <v-col cols="12" sm="7" offset-sm="5" md="9" offset-md="3" xl="2" offset-xl="10" :class="{
          'text-right': $vuetify.breakpoint.smAndUp,
        }">
          <add-button text="Agregar imagen" :block="$vuetify.breakpoint.xs" @click="$refs.dialogImage.showDialog({
            id: null,
            productNameId: product.product_name_id,
            colorId: product.color_id,
            file: null,
            url: null,
            video: false,
            order: 0,
          })" />
        </v-col>
      </v-row>
      <draggable v-model="images" group="images" tag="v-row" v-bind="dragOptions" @start="drag = true" @end="endDrag">
        <v-col v-for="item in images" :key="item.id" cols="12" sm="6" md="4" lg="3" xl="2">
          <v-img :src="item.url" :alt="item.url">
            <template v-slot:placeholder>
              <v-row class="fill-height ma-0" align="center" justify="center">
                <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
              </v-row>
            </template>
            <div class="d-flex justify-end align-end fill-height">
              <v-btn x-small fab icon color="warning" class="mr-1 mb-1">
                <v-icon>mdi-cursor-move</v-icon>
              </v-btn>
              <v-btn x-small fab icon color="error" class="mr-1 mb-1" @click="$refs.imageRemove.showDialog(item)">
                <v-icon>mdi-close</v-icon>
              </v-btn>
              <v-btn x-small fab icon color="info" class="mr-1 mb-1" @click="$refs.dialogImage.showDialog({
                ...item,
                productNameId: item.product_name_id,
                colorId: item.color_id,
                path: null,
                url: item.path ? null : item.url,
              })">
                <v-icon>mdi-pencil</v-icon>
              </v-btn>
            </div>
          </v-img>
        </v-col>
      </draggable>
    </v-card>
    <image-remove ref="imageRemove" @updateList="fetchImages" />
    <product-image ref="dialogImage" @updateImage="fetchImages" />
  </v-container>
</template>

<script>
import draggable from 'vuedraggable'

export default {
  name: "ProductImages",
  components: {
    draggable,
    "image-remove": () => import("@/components/products/ImageRemove.vue"),
    "building-details": () => import("@/components/shared/BuildingDetails.vue"),
    "product-image": () => import("@/components/products/ProductImageForm.vue"),
  },
  data() {
    return {
      drag: false,
      store: {},
      product: {},
      images: [],
    };
  },
  computed: {
    dragOptions() {
      return {
        animation: 200,
        group: 'images',
        disabled: false,
        ghostClass: 'ghost',
      };
    },
    isBuilding() {
      return this.$route.params.storeId != undefined;
    },
    isWarehouse() {
      return this.store.warehouse;
    },
    breadcrumbs() {
      if (this.isBuilding) {
        return [
          {
            text: this.isWarehouse ? "Almacenes" : "Tiendas",
            disabled: false,
            to: {
              path: this.isWarehouse ? "/warehouses" : "/stores",
            },
          },
          {
            text: "Inventario",
            disabled: false,
            to: {
              path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products`,
            },
          },
          {
            text: "Detalle",
            disabled: true,
            to: {
              path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products/${this.$route.params.productNameId}`,
              query: {
                size_type_id: this.$route.query.size_type_id,
              },
            },
          },
          {
            text: "Imágenes",
            disabled: true,
            to: {
              path: `/${this.$route.params.storeType}/${this.$route.params.storeId}/products/${this.$route.params.productNameId}/images/${this.$route.params.productId}`,
              query: {
                size_type_id: this.$route.query.size_type_id,
              },
            },
          },
        ];
      } else {
        return [
          {
            text: "Productos",
            disabled: false,
            to: {
              path: `/products`,
            },
          },
          {
            text: "Detalle",
            disabled: true,
            to: {
              path: `/products/${this.$route.params.productNameId}`,
              query: {
                size_type_id: this.$route.query.size_type_id,
              },
            },
          },
          {
            text: "Imágenes",
            disabled: true,
            to: {
              path: `/products/${this.$route.params.productNameId}/images/${this.$route.params.productId}`,
              query: {
                size_type_id: this.$route.query.size_type_id,
              },
            },
          },
        ];
      }
    },
  },
  mounted() {
    this.fetchProduct();
  },
  methods: {
    async endDrag(event) {
      this.drag = false;
      try {
        this.$store.dispatch("loading", true);
        for (let i = 0; i < this.images.length; i++) {
          this.images[i].order = i + 1
        }
        await axios.post(
          `product/${this.$route.params.productId}/color/${this.$route.params.colorId}/images/order`,
          {
            images: this.images,
          },
        );
      } catch (error) {
        this.fetchImages();
        console.error(error);
      } finally {
        this.$store.dispatch("loading", false);
      }
    },
    isActive(active) {
      return active == true;
    },
    async fetchProduct() {
      try {
        let response = await axios.get(
          `product/${this.$route.params.productId}/details`,
          {
            params: {
              size_type_id: this.$route.query.size_type_id,
              store_id: this.$route.params.storeId,
            },
          }
        );
        this.product = response.data.payload;
        this.fetchImages();
      } catch (error) {
        console.error(error);
      } finally {
        if (this.isBuilding) {
          this.fetchStore();
        }
      }
    },
    async fetchStore() {
      try {
        let response = await axios.get(`store/${this.$route.params.storeId}`);
        this.store = response.data.payload.store;
      } catch (error) {
        console.error(error);
      }
    },
    async fetchImages() {
      try {
        this.$store.dispatch("loading", true);
        let response = await axios.get(
          `product/${this.$route.params.productId}/color/${this.$route.params.colorId}/images`,
        );
        this.images = response.data.payload.data;
      } catch (error) {
        console.error(error);
      } finally {
        this.$store.dispatch("loading", false);
      }
    },
  },
};
</script>

<style>
.ghost {
  opacity: 0.5;
  background: #37474F;
}
</style>