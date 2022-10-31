import Vue from 'vue'

Vue.prototype.$helpers = {
  listIndex: function(index, options) {
    return index + 1 + (options.itemsPerPage * (options.page-1))
  },
}
