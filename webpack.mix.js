const mix = require('laravel-mix');

mix.sass('resources/scss/main.scss', 'dist/css/statamic-one-click-content-translation.css');
mix
    .js('resources/js/index.js', 'dist/js/statamic-one-click-content-translation.js')
    .setPublicPath('dist');
