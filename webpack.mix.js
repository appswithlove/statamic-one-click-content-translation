const mix = require('laravel-mix');

mix
    .js('resources/js/index.js', 'resources/dist/js/translate-me.js')
    .setPublicPath('resources/dist');