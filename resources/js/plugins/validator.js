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
      name: 'nombre',
      active: 'activo',
      code: 'código',
      person_id: 'nombre',
      order: 'orden',
      comment: 'glosa',
      total_price: 'precio total',
      movement_type_id: 'tipo de movimiento',
      user_id: 'usuario',
      client_id: 'icliente',
      from_store_id: 'tienda origen',
      to_store_id: 'tienda destino',
      movement_id: 'movimiento',
      product_id: 'producto',
      stock: 'stock',
      store_id: 'tienda/almacén',
      icon: 'ícono',
      entry: 'ingreso',
      document: 'documento',
      document_type_id: 'tipo de documento',
      address: 'dirección',
      email: 'email',
      phone: 'teléfono',
      city_id: 'ciudad',
      product_name_id: 'producto',
      brand_id: 'marca',
      gender_id: 'género',
      size_id: 'talla',
      color_id: 'color',
      category_id: 'categoría',
      sell_price: 'precio de venta',
      size_type_id: 'tipo de talla',
      numeric: 'numérico',
      warehouse: 'almacén',
      username: 'usuario',
      password: 'contraseña',
      access_attempts: 'intentos de acceso',
      role_id: 'rol',
      details: 'detalles',
      category_name: 'categoría',
      sizes: 'tallas',
      brands: 'marcas',
      colors: 'colores',
      old_password: 'contraseña actual',
      search: 'Texto o parámetro de búsqueda',
      date_from: 'fecha de inicio',
      date_to: 'fecha de caducidad',
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
