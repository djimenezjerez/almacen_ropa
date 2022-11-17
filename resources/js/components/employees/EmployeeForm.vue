<template>
  <v-dialog
    v-model="dialog"
    persistent
    max-width="600"
    @keydown.esc="dialog = false"
  >
    <v-card>
      <template slot="progress">
        <progress-bar />
      </template>
      <v-toolbar dense dark color="secondary">
        <tool-bar-title :title="readOnly ? 'Datos de empleado' : (edit ? 'Editar empleado' : 'Agregar empleado')"/>
        <v-spacer></v-spacer>
        <v-btn
          icon
          @click.stop="dialog = false"
        >
          <v-icon>
            mdi-close
          </v-icon>
        </v-btn>
      </v-toolbar>
      <div class="px-5 pb-5">
        <validation-observer ref="employeeObserver" v-slot="{ invalid }">
          <v-form @submit.prevent="submit" :readonly="readOnly">
            <v-card-text>
              <v-row dense>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="user_id"
                    rules="required|numeric"
                  >
                    <v-autocomplete
                      label="Nombre"
                      v-model="employeeForm.user_id"
                      item-text="name"
                      item-value="id"
                      :items="filteredUsers"
                      data-vv-name="user_id"
                      :error-messages="errors"
                      prepend-icon="mdi-account-circle"
                      :autofocus="!readOnly && !edit"
                      :readonly="edit"
                    ></v-autocomplete>
                  </validation-provider>
                </v-col>
                <v-col cols="12">
                  <validation-provider
                    v-slot="{ errors }"
                    name="role_id"
                    rules="required|numeric"
                  >
                    <v-select
                      :items="roles"
                      item-text="display_name"
                      item-value="id"
                      label="Rol"
                      v-model="employeeForm.role_id"
                      data-vv-name="role_id"
                      :error-messages="errors"
                      prepend-icon="mdi-key"
                      :autofocus="!readOnly && edit"
                    ></v-select>
                  </validation-provider>
                </v-col>
              </v-row>
            </v-card-text>
            <v-card-actions>
              <v-row dense justify="end">
                <v-col cols="12" md="6">
                  <v-btn
                    block
                    type="submit"
                    color="info"
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
  name: 'EmployeeForm',
  props: {
    users: {
      type: Array,
      required: true
    },
    employees: {
      type: Array,
      required: true
    },
    store: {
      type: Object,
      required: true
    },
  },
  computed: {
    filteredUsers() {
      if (this.readOnly || this.employeeForm.user_id) {
        return this.users.filter(i => i.id == this.employeeForm.user_id)
      } else {
        return this.users.filter(i => (!this.employees.includes(i.id) && i.id != this.$store.getters.user.id))
      }
    }
  },
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      roles: [],
      employeeForm: {
        user_id: null,
        role_id: null,
      },
    }
  },
  methods: {
    showDialog(employee = null, readOnly = false) {
      this.fetchRoles()
      this.readOnly = readOnly
      if (employee) {
        this.edit = true
        this.employeeForm = {
          user_id: employee.user_id,
          role_id: employee.role_id,
        }
      } else {
        this.edit = false
        this.employeeForm = {
          user_id: null,
          role_id: null,
        }
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.employeeObserver.reset()
      })
    },
    async fetchRoles() {
      try {
        let response = await axios.get('role', {
          params: {
            combo: true,
            warehouse: Number(this.store.warehouse),
          }
        })
        this.roles = response.data.payload.data
      } catch(error) {
        console.error(error)
      }
    },
    async submit() {
      try {
        let valid = await this.$refs.employeeObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          const response = await axios.post(`store/${this.store.id}/employee`, this.employeeForm)
          this.$toast.success(response.data.message)
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.employeeObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.employeeObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
