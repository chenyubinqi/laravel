let mix = require('laravel-mix');
const path = require('path');

mix.webpackConfig({
    resolve: {
        alias: {
            '@': 'assets/js',
            'components': 'assets/js/components',
            'views': 'assets/js/views',
            'router': 'assets/js/router',
        },
        modules: [
            'node_modules',
            path.resolve(__dirname, "resources")
        ]
    },

});

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
// .extract(['vue', 'vue-resource', 'lodash', 'jquery', 'element-ui'])
    .sass('resources/assets/sass/app.scss', 'public/css')
    .version();
