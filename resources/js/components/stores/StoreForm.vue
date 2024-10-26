<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="640"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title
          :title="
            (readOnly ? 'Datos de ' : edit ? 'Editar ' : 'Agregar ') +
            (warehouse ? 'almacén' : 'tienda')
          "
        />
        <v-spacer></v-spacer>
        <v-btn icon @click.stop="dialog = false">
          <v-icon> mdi-close </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="storeObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit" :readonly="readOnly">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="name"
                    rules="required|min:3"
                  >
                    <v-text-field
                      label="Nombre *"
                      v-model="storeForm.name"
                      data-vv-name="name"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle"
                      autofocus
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="address"
                    rules="min:3"
                  >
                    <v-text-field
                      label="Direcciòn"
                      v-model="storeForm.address"
                      data-vv-name="address"
                      :error-messages="errors"
                      prepend-icon="mdi-map-marker"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6" v-if="!warehouse">
                  <validation-provider
                    v-slot="{ errors }"
                    name="document"
                    rules="required|min:3|alpha_dash"
                  >
                    <v-text-field
                      label="NIT *"
                      v-model="storeForm.document"
                      data-vv-name="document"
                      :error-messages="errors"
                      prepend-icon="mdi-card-account-details"
                      @input="
                        (value) => (storeForm.document = value.toUpperCase())
                      "
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="city_id"
                    rules="integer"
                  >
                    <v-select
                      :items="cities"
                      item-text="name"
                      item-value="id"
                      label="Ciudad"
                      v-model="storeForm.city_id"
                      data-vv-name="city_id"
                      :error-messages="errors"
                      prepend-icon="mdi-map"
                    ></v-select>
                  </validation-provider>
                </v-col>
                <v-col cols="12" md="6">
                  <validation-provider
                    v-slot="{ errors }"
                    name="phone"
                    rules="min:7|integer"
                  >
                    <v-text-field
                      label="Teléfono"
                      v-model="storeForm.phone"
                      data-vv-name="phone"
                      :error-messages="errors"
                      prepend-icon="mdi-phone"
                    ></v-text-field>
                  </validation-provider>
                </v-col>
                <template v-if="!warehouse">
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="email"
                      rules="email"
                    >
                      <v-text-field
                        label="Email"
                        v-model="storeForm.email"
                        data-vv-name="email"
                        :error-messages="errors"
                        prepend-icon="mdi-at"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="whatsapp"
                      rules=""
                    >
                      <v-text-field
                        label="Whatsapp"
                        v-model="storeForm.whatsapp"
                        data-vv-name="whatsapp"
                        :error-messages="errors"
                        prepend-icon="mdi-whatsapp"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="facebook"
                      rules=""
                    >
                      <v-text-field
                        label="Facebook"
                        v-model="storeForm.facebook"
                        data-vv-name="facebook"
                        :error-messages="errors"
                        prepend-icon="mdi-facebook"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="youtube"
                      rules=""
                    >
                      <v-text-field
                        label="YouTube"
                        v-model="storeForm.youtube"
                        data-vv-name="youtube"
                        :error-messages="errors"
                        prepend-icon="mdi-youtube"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="instagram"
                      rules=""
                    >
                      <v-text-field
                        label="Instagram"
                        v-model="storeForm.instagram"
                        data-vv-name="instagram"
                        :error-messages="errors"
                        prepend-icon="mdi-instagram"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="tiktok"
                      rules=""
                    >
                      <v-text-field
                        label="TikTok"
                        v-model="storeForm.tiktok"
                        data-vv-name="tiktok"
                        :error-messages="errors"
                        prepend-icon="mdi-music-note"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider
                      v-slot="{ errors }"
                      name="pinterest"
                      rules=""
                    >
                      <v-text-field
                        label="Pinterest"
                        v-model="storeForm.pinterest"
                        data-vv-name="pinterest"
                        :error-messages="errors"
                        prepend-icon="mdi-pinterest"
                      ></v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6" v-if="!readOnly">
                    <validation-provider
                      v-slot="{ errors }"
                      name="file"
                      :rules="edit ? '' : 'required|image'"
                    >
                      <v-file-input
                        accept="image/*"
                        label="Logo"
                        v-model="storeForm.file"
                        data-vv-name="file"
                        :error-messages="errors"
                        prepend-icon="mdi-image"
                      ></v-file-input>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6" v-else>
                    <v-img cover :src="storeForm.logo"></v-img>
                  </v-col>
                </template>
                <v-col cols="12" md="6" v-if="edit">
                  <validation-provider
                    v-slot="{ errors }"
                    name="active"
                    rules="required"
                  >
                    <v-checkbox
                      label="Activo"
                      v-model="storeForm.active"
                      data-vv-name="active"
                      :error-messages="errors"
                      prepend-icon="mdi-check-all"
                    ></v-checkbox>
                  </validation-provider>
                </v-col>
                <template v-if="!warehouse">
                  <v-col cols="12" md="6" v-if="!readOnly">
                    <validation-provider
                      v-slot="{ errors }"
                      name="fileQR"
                      rules="image"
                    >
                      <v-file-input
                        accept="image/*"
                        label="QR"
                        v-model="storeForm.fileQR"
                        data-vv-name="fileQR"
                        :error-messages="errors"
                        prepend-icon="mdi-image"
                      ></v-file-input>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6" v-else>
                    <v-img cover :src="storeForm.qr"></v-img>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-menu
                      ref="datePicker"
                      v-model="datePicker"
                      :close-on-content-click="false"
                      transition="scale-transition"
                      offset-y
                      max-width="290px"
                      min-width="auto"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <validation-provider
                          v-slot="{ errors }"
                          name="qr_due_date"
                          rules="required_if:fileQR"
                        >
                          <v-text-field
                            v-model="computedDateFormatted"
                            label="Fecha límite QR"
                            prepend-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                            :disabled="!storeForm.fileQR"
                            data-vv-name="qr_due_date"
                            :error-messages="errors"
                          ></v-text-field>
                        </validation-provider>
                      </template>
                      <v-date-picker
                        v-model="storeForm.qr_due_date"
                        no-title
                        @input="datePicker = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-col>
                </template>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-row dense justify="end">
                <v-col cols="12" md="6">
                  <v-btn
                    block
                    type="submit"
                    color="success"
                    :disabled="invalid"
                    v-if="!readOnly"
                  >
                    Guardar
                  </v-btn>
                  <v-btn
                    block
                    color="error"
                    v-else
                    @click.stop="dialog = false"
                  >
                    Cerrar
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
  name: "StoreForm",
  props: {
    cities: {
      type: Array,
      required: true,
    },
    warehouse: {
      type: Boolean,
      required: true,
    },
  },
  computed: {
    computedDateFormatted() {
      return this.formatDate(this.storeForm.qr_due_date);
    },
  },
  data: function () {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      datePicker: false,
      storeForm: {
        id: null,
        file: null,
        fileQR: null,
        name: null,
        active: true,
        document: null,
        address: null,
        email: null,
        phone: null,
        city_id: null,
        qr_due_date: new Date(
          Date.now() - new Date().getTimezoneOffset() * 60000
        )
          .toISOString()
          .substr(0, 10),
        warehouse: Number(this.warehouse),
        whatsapp: null,
        facebook: null,
        youtube: null,
        instagram: null,
        tiktok: null,
        pinterest: null,
      },
    };
  },
  methods: {
    formatDate(date) {
      if (!date) return null;
      const [year, month, day] = date.split("-");
      return `${day}/${month}/${year}`;
    },
    showDialog(store = null, readOnly = false) {
      this.readOnly = readOnly;
      if (store) {
        this.edit = true;
        this.storeForm = {
          ...store,
          file: null,
          fileQR: null,
        };
      } else {
        this.edit = false;
        this.storeForm = {
          id: null,
          file: null,
          fileQR: null,
          name: null,
          active: true,
          document: null,
          address: null,
          email: null,
          phone: null,
          city_id: null,
          qr_due_date: new Date(
            Date.now() - new Date().getTimezoneOffset() * 60000
          )
            .toISOString()
            .substr(0, 10),
          warehouse: Number(this.warehouse),
          whatsapp: null,
          facebook: null,
          youtube: null,
          instagram: null,
          tiktok: null,
          pinterest: null,
        };
      }
      this.dialog = true;
      this.$nextTick(() => {
        this.$refs.storeObserver.reset();
      });
    },
    async submit() {
      try {
        let valid = await this.$refs.storeObserver.validate();
        if (valid) {
          this.$store.dispatch("loading", true);
          if (this.edit) {
            const response = await axios.patch(
              `store/${this.storeForm.id}`,
              this.storeForm
            );
            if (this.storeForm.file != null) {
              let formData = new FormData();
              formData.append("id", this.storeForm.id);
              formData.append("file", this.storeForm.file);
              await axios.post(`store/${this.storeForm.id}/logo`, formData, {
                headers: { "Content-Type": "multipart/form-data" },
              });
            }
            if (this.storeForm.fileQR != null) {
              let formData = new FormData();
              formData.append("id", this.storeForm.id);
              formData.append("file", this.storeForm.fileQR);
              formData.append("qr_due_date", this.storeForm.qr_due_date);
              await axios.post(`store/${this.storeForm.id}/qr`, formData, {
                headers: { "Content-Type": "multipart/form-data" },
              });
            }
            this.$toast.success(response.data.message);
          } else {
            const response = await axios.post("store", this.storeForm);
            if (this.storeForm.file != null) {
              let formData = new FormData();
              formData.append("id", response.data.payload.store.id);
              formData.append("file", this.storeForm.file);
              await axios.post(
                `store/${response.data.payload.store.id}/logo`,
                formData,
                {
                  headers: { "Content-Type": "multipart/form-data" },
                }
              );
            }
            if (this.storeForm.fileQR != null) {
              let formData = new FormData();
              formData.append("id", response.data.payload.store.id);
              formData.append("file", this.storeForm.fileQR);
              formData.append("qr_due_date", this.storeForm.qr_due_date);
              await axios.post(
                `store/${response.data.payload.store.id}/qr`,
                formData,
                {
                  headers: { "Content-Type": "multipart/form-data" },
                }
              );
            }
            this.$toast.success(response.data.message);
          }
          this.$emit("updateList");
          this.dialog = false;
        }
      } catch (error) {
        this.$refs.storeObserver.reset();
        if ("errors" in error.response.data) {
          this.$refs.storeObserver.setErrors(error.response.data.errors);
        }
      } finally {
        this.$store.dispatch("loading", false);
      }
    },
  },
};
</script>
