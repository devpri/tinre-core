let mix = require('laravel-mix')
let tailwindcss = require('tailwindcss')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js('resources/js/app.js', 'public')
  .js('resources/js/dashboard.js', 'public')
  .extract([
    '@fortawesome/vue-fontawesome',
    'axios',
    'chart.js',
    'moment',
    'popper.js',
    'noty',
    'v-tooltip',
    'vue-chartjs',
    'vue-clickaway',
    'vue-js-modal',
    'vue-router',
    'vue2-datepicker',
    'vue',
  ])
  .setPublicPath('public')
  .sass('resources/sass/app.scss', 'public')
  .options({
    processCssUrls: false,
    postCss: [tailwindcss('./tailwind.config.js')],
  })
  .copy('public', '../tinre-core-test/public/vendor/tinre')
  .webpackConfig({
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'resources/js/'),
        '~': path.join(__dirname, './resources/js'),
      },
    },
  })
  .version()
