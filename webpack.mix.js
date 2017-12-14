let mix = require('laravel-mix');

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

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css');
mix.scripts([
    'public/plugins/lazyload/jquery.lazyload.js',
    'public/js/script.js',
    'public/plugins/swal/sweetalert2.min.js',
    'public/plugins/qrcode/jquery-qrcode.js',
], 'public/js/app.front.js');
mix.version();
