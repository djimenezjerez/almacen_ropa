const mix = require('laravel-mix')
const path = require('path')
require('vuetifyjs-mix-extension')

if (!process.argv.includes('--hot')) {
  mix.version()
}

mix
.webpackConfig({
  stats: {
    children: true,
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/js'),
      '~': path.resolve(__dirname, 'resources/sass')
    },
  },
  devtool: "inline-source-map"
})
.js('resources/js/app.js', 'public/js')
.sass('resources/sass/app.scss', 'public/css')
.copy('resources/fonts', 'public/fonts')
.copy('resources/img', 'public/img')
.minify('public/css/app.css')
.vuetify(
  'vuetify-loader',
  'resources/sass/variables.scss',
  {
    progressiveImages: true
  }
)
.vue()
.sourceMaps(!(process.env.NODE_ENV == 'production'))
.disableNotifications()
