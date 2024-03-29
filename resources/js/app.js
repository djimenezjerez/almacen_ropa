require('@/bootstrap')
window.Vue = require('vue').default

import Vue from 'vue'
import vuetify from '@/plugins/vuetify'
import router from '@/plugins/router'
import store from '@/plugins/store'
import '@/helpers'

import ToolBarTitle from '@/components/shared/ToolBarTitle'
import ProgressBar from '@/components/shared/ProgressBar'
import AddButton from '@/components/shared/AddButton'
import SearchInput from '@/components/shared/SearchInput'
import DialogRemove from '@/components/shared/DialogRemove'
import LoadingOverlay from '@/components/shared/LoadingOverlay'

Vue.component('tool-bar-title', ToolBarTitle)
Vue.component('progress-bar', ProgressBar)
Vue.component('add-button', AddButton)
Vue.component('search-input', SearchInput)
Vue.component('dialog-remove', DialogRemove)
Vue.component('loading-overlay', LoadingOverlay)

Vue.prototype.$headerClass = 'blue-grey darken-2 white--text body'

export const bus = new Vue()

new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
