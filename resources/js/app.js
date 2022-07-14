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

Vue.component('tool-bar-title', ToolBarTitle)
Vue.component('progress-bar', ProgressBar)
Vue.component('add-button', AddButton)
Vue.component('search-input', SearchInput)
Vue.component('dialog-remove', DialogRemove)

export const bus = new Vue()

new Vue({
  store,
  router,
  vuetify,
  el: '#app',
})
