import Vue from 'vue'

Vue.prototype.$helpers = {
  listIndex: function(index, options) {
    return index + 1 + (options.itemsPerPage * (options.page-1))
  },
  stockExceded: function(product) {
    try {
      const total = parseInt(product.total_stock)
      const stock = parseInt(product.stock)
      if (total < stock || stock < 1) {
        return true
      } else {
        return false
      }
    } catch(error) {
      return true
    }
  },
}
