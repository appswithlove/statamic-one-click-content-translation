const mix = require('laravel-mix');

mix.sass('resources/scss/main.scss', 'css/statamic-one-click-content-translation.css');
mix
    .js('resources/js/index.js', 'js/statamic-one-click-content-translation.js')
    .vue()
    .setPublicPath('dist');
