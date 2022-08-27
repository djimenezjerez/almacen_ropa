<template>
  
</template>

<script>
export default {
  name: 'productForm',
  data: function() {
    return {
      dialog: false,
      readOnly: false,
      edit: false,
      productForm: {
        id: null,
        name: null,
        active: true,
        category_name: null,
        brand_name: null,
        size_name: null,
        color_name: null,
        size_type_id: null,
      },
    }
  },
  methods: {
    showDialog(product = null, readOnly = false) {
      this.readOnly = readOnly
      if (product) {
        this.edit = true
        this.productForm = {
          ...product
        }
      } else {
        this.edit = false
        this.productForm = {
          id: null,
          name: null,
          active: true,
          category_name: null,
          brand_name: null,
          size_name: null,
          color_name: null,
          size_type_id: null,
        }
        this.fetchProducts()
      }
      this.dialog = true
      this.$nextTick(() => {
        this.$refs.productObserver.reset()
      })
    },
    async submit() {
      try {
        let valid = await this.$refs.productObserver.validate()
        if (valid) {
          this.$store.dispatch('loading', true)
          if (this.edit) {
            const response = await axios.patch(`product/${this.productForm.id}`, this.productForm)
            this.$toast.success(response.data.message)
          } else {
            const response = await axios.post('product', this.productForm)
            this.$toast.success(response.data.message)
          }
          this.$emit('updateList')
          this.dialog = false
        }
      } catch(error) {
        this.$refs.productObserver.reset()
        if ('errors' in error.response.data) {
          this.$refs.productObserver.setErrors(error.response.data.errors)
        }
      } finally {
        this.$store.dispatch('loading', false)
      }
    }
  },
}
</script>
