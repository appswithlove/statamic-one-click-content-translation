const mix = require('laravel-mix');

mix
    .js('resources/js/index.js', 'dist/js/translate-me.js')
    .setPublicPath('dist');