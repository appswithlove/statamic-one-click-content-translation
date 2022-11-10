const mix = require('laravel-mix');

mix.sass('resources/scss/main.scss', 'dist/css/translate-me.css');
mix
    .js('resources/js/index.js', 'dist/js/translate-me.js')
    .setPublicPath('dist');
