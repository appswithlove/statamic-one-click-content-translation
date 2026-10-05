const mix = require('laravel-mix');

mix.css('resources/css/main.css', 'css/statamic-one-click-content-translation.css');
mix
    .js('resources/js/index.js', 'js/statamic-one-click-content-translation.js')
    .vue()
    .setPublicPath('dist');
