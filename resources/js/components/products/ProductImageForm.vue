<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="400"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title title="Cargar imagen" />
        <v-spacer></v-spacer>
        <v-btn icon @click.stop="dialog = false">
          <v-icon> mdi-close </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="formObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="url"
                    rules="min:0|max:255"
                  >
                    <v-text-field
                      label="URL de imagen"
                      v-model.number="form.url"
                      data-vv-name="url"
                      :error-messages="errors"
                      prepend-icon="mdi-web"
                    ></v-text-field>
                  </validation-provider>
                  <validation-provider v-slot="{ errors }" name="file" rules="">
                    <v-file-input
                      accept="image/*"
                      label="Cargar imagen"
                      v-model="form.file"
                      data-vv-name="file"
                      :error-messages="errors"
                      prepend-icon="mdi-image"
                    ></v-file-input>
                  </validation-provider>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-row dense justify="end">
                <v-col cols="12" md="6">
                  <v-btn block type="submit" color="info" :disabled="invalid">
                    Aceptar
                  </v-btn>
                </v-col>
                <v-col cols="12" md="6">
                  <v-btn block color="error" @click.stop="dialog = false">
                    Cancelar
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-actions>
          </v-form>
        </validation-observer>
      </div>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: "ProductImageForm",
  data: function () {
    return {
      dialog: false,
      form: {
        id: null,
        file: null,
        url: null,
      },
    };
  },
  methods: {
    showDialog(product) {
      this.form.id = product.id;
      this.form.file = null;
      this.form.url = null;
      this.dialog = true;
      this.$nextTick(() => {
        this.$refs.formObserver.reset();
      });
    },
    async submit() {
      try {
        let valid = await this.$refs.formObserver.validate();
        if (valid) {
          let formData = new FormData();
          formData.append("id", this.form.id);
          if (this.form.url != null && this.form.url != "") {
            formData.append("url", this.form.url);
          } else {
            formData.append("file", this.form.file);
          }
          this.$store.dispatch("loading", true);
          const response = await axios.post(
            `product/${this.form.id}/image`,
            formData,
            {
              headers: { "Content-Type": "multipart/form-data" },
            }
          );
          this.$toast.success(response.data.message);
          this.$emit("updateImage", response.data.color);
          this.dialog = false;
        }
      } catch (error) {
        this.$refs.formObserver.reset();
        if ("errors" in error.response.data) {
          this.$refs.formObserver.setErrors(error.response.data.errors);
        }
      } finally {
        this.$store.dispatch("loading", false);
      }
    },
  },
};
</script>
