const mix = require('laravel-mix');

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

// CSS
mix.sass('resources/sass/app.scss', 'resources/css/app.css');

mix.styles([
    'resources/css/bootstrap.css',
    'resources/css/AdminLTE.css',
    'resources/css/skin-blue.css',
    'resources/css/app.css',
], 'public/css/all.css');

// Javascript
mix.babel([
    'app.js'
], 'resources/js/app.js');

mix.scripts([
    'resources/js/bootstrap.js',
    'resources/js/AdminLTE.js',
    'resources/js/scripts.js',
    'resources/js/app.js'
], 'public/js/all.js');