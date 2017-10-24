let mix = require('laravel-mix');
const path = require('path');

mix.webpackConfig({
    module: {
        rules: [
            // {
            //     test: /\.css$/,
            //     loader: "style-loader!css-loader"
            // },
        ]
    },
    resolve: {
        alias: {
            '@': 'assets/js',
            'components': path.resolve(__dirname, 'assets/js/components'),
            'views': path.resolve(__dirname, 'assets/js/views'),
            'router': path.resolve(__dirname, 'assets/js/router'),
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
