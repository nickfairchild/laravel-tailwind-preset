const mix = require('laravel-mix')

require('laravel-mix-purgecss')

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css',[
        require('postcss-import'),
        require('tailwindcss'),
        require('postcss-nested'),
        require('autoprefixer'),
    ])

if (mix.inProduction()) {
    mix
        .version()
        .purgeCss()
}
