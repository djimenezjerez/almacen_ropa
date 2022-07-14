import Vue from 'vue'
import store from '@/plugins/store.js'

Vue.prototype.$helpers = {
  listIndex: function(index, options) {
    return index + 1 + (options.itemsPerPage * (options.page-1))
  },
}
