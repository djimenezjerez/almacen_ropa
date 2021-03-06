import Vue from 'vue'
import { localize, extend, ValidationObserver, ValidationProvider } from 'vee-validate'
import * as rules from 'vee-validate/dist/rules'
import en from 'vee-validate/dist/locale/en.json'
import es from 'vee-validate/dist/locale/es.json'

Vue.component('validation-observer', ValidationObserver)
Vue.component('validation-provider', ValidationProvider)

localize({
  en,
  es,
})

localize({
  es: {
    messages: {
      strong_password: (field) => `La ${field} debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 símbolo (E.g. , . _ & ? etc)`,
    },
    names: {
      first_name: 'nombre',
      last_name: 'apellido',
      identity_card: 'documento de identidad',
      password: 'contraseña',
      old_password: 'contraseña actual',
      phone: 'teléfono',
      address: 'dirección',
      search: 'Texto o parámetro de búsqueda',
      search_by: 'parámetro de búsqueda',
      active: 'activo',
      from_date: 'fecha de inicio',
      to_date: 'fecha de caducidad',
      city_id: 'ciudad',
      role_id: 'rol',
    }
  },
})

localize('es')

Object.keys(rules).forEach(rule => {
  extend(rule, rules[rule])
})

extend('strong_password', {
  validate: value => {
      var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})")
      return strongRegex.test(value)
  }
})
