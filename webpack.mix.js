const mix = require('laravel-mix')

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue'],
        alias: {
            '@': __dirname + 'resources',
        },
    },
    output: {
        chunkFilename: 'assets/js/chunks/[name].js',
    },
})

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
mix.copyDirectory('resources/js/assets/images', 'public/assets/images')
mix.copyDirectory('resources/js/assets/fonts', 'public/assets/fonts')
mix.copy('resources/assets/js/jquery.min.js', 'public/assets/js')
mix.copy('resources/assets/js/jquery.dataTables.min.js', 'public/assets/js')
mix.js('resources/js/app.js', 'public/assets/js').
    sass('resources/js/assets/scss/style.scss', 'public/assets/css').
    sass('resources/assets/landing-page-scss/scss/landing-page-style.scss', 'public/assets/css').version();
