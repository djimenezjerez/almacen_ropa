import Vue from 'vue'
import Vuetify from 'vuetify'
import VuetifyToast from 'vuetify-toast-snackbar'
import es from 'vuetify/src/locale/es.ts'

Vue.use(Vuetify)

const vuetify = new Vuetify({
  lang: {
    locales: { es },
    current: 'es',
  },
  icons: {
    iconfont: 'mdi',
  },
  theme: {
    dark: false,
    options: {
      customProperties: true,
    },
    themes: {
      light: {
        primary: '#37474F',
        secondary: '#78909C',
        tertiary: '#CFD8DC',
        info: '#0091EA',
        warning: '#FF9800',
        error: '#DD2C00',
        accent: '#004D40',
        background: '#E0F2F1',
      },
    },
  },
})

Vue.use(VuetifyToast, {
  $vuetify: vuetify.framework,
  timeout: 5000,
  x: 'center',
  y: 'top',
})

export default vuetify
