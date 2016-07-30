var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {

    // Sass
    mix.sass('app.scss', 'resources/assets/css/app.css');

    // CSS
    mix.styles([
        'bootstrap.css',
        'AdminLTE.css',
        'skin-blue.css',
        'app.css',
    ]);

    // Javascript
    mix.scripts([
        'bootstrap.js',
        'AdminLTE.js',
        'scripts.js'
    ]);

});
