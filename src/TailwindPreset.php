<?php

namespace Nickfairchild\TailwindPreset;

use Illuminate\Support\Arr;
use Laravel\Ui\Presets\Preset;
use Illuminate\Filesystem\Filesystem;

class TailwindPreset extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::updatePackages();
        static::updateStyles();
        static::updateWebpackConfiguration();
        static::updateBootstrapping();
        static::updateWelcomePage();
        static::removeNodeModules();
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return [
                'laravel-mix' => '^4.0',
                'laravel-mix-purgecss' => '^4.1',
                'tailwindcss' => '^1.2',
                '@tailwindcss/custom-forms' => '^0.2',
                'postcss-import' => '^12.0',
                'autoprefixer' => '^9.7',
                'postcss-nested' => '^4.2'
            ] + Arr::except($packages, [
                'bootstrap',
                'bootstrap-sass',
                'popper.js',
                'laravel-mix',
                'jquery',
            ]);
    }

    /**
     * Update the css files.
     *
     * @return void
     */
    protected static function updateStyles()
    {
        tap(new Filesystem, function ($filesystem) {
            $filesystem->deleteDirectory(resource_path('sass'));
            $filesystem->delete(public_path('js/app.js'));
            $filesystem->delete(public_path('css/app.css'));

            if (! $filesystem->isDirectory($directory = resource_path('css'))) {
                $filesystem->makeDirectory($directory, 0755, true);
            }
        });

        copy(__DIR__.'/stubs/resources/css/app.css', resource_path('css/app.css'));
    }

    /**
     * Update the Webpack configuration.
     *
     * @return void
     */
    protected static function updateWebpackConfiguration()
    {
        copy(__DIR__.'/stubs/webpack.mix.js', base_path('webpack.mix.js'));
    }

    /**
     * Update bootstrapping files.
     *
     * @return void
     */
    protected static function updateBootstrapping()
    {
        copy(__DIR__.'/stubs/tailwind.config.js', base_path('tailwind.config.js'));
        copy(__DIR__.'/stubs/resources/js/bootstrap.js', resource_path('js/bootstrap.js'));
    }

    /**
     * Update welcome page.
     *
     * @return void
     */
    protected static function updateWelcomePage()
    {
        (new Filesystem)->delete(resource_path('views/welcome.blade.php'));

        copy(__DIR__.'/stubs/resources/views/welcome.blade.php', resource_path('views/welcome.blade.php'));
    }
}
