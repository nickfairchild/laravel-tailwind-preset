<?php

namespace Nickfairchild\TailwindPreset;

use Illuminate\Support\Arr;
use Laravel\Ui\Presets\Preset;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Container\Container;

class TailwindPreset extends Preset
{
    protected static $views = [
        'auth/login.blade.php',
        'auth/passwords/confirm.blade.php',
        'auth/passwords/email.blade.php',
        'auth/passwords/reset.blade.php',
        'auth/register.blade.php',
        'auth/verify.blade.php',
        'home.blade.php',
        'layouts/app.blade.php',
    ];

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

    public static function auth()
    {
        static::ensureDirectoryExists();
        static::exportViews();
        static::exportBackend();
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

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected static function ensureDirectoryExists()
    {
        if (! is_dir($directory = static::getViewPath('layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = static::getViewPath('auth/passwords'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected static function exportViews()
    {
        foreach (static::$views as $value) {
            copy(
                __DIR__.'/stubs/'.$value,
                static::getViewPath($value)
            );
        }
    }

    /**
     * Export the authentication backend.
     *
     * @return void
     */
    protected static function exportBackend()
    {
        file_put_contents(
            app_path('Http/Controllers/HomeController.php'),
            static::compileControllerStub()
        );

        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/stubs/routes.stub'),
            FILE_APPEND
        );

        copy(
            __DIR__.'/stubs/migrations/2014_10_12_100000_create_password_resets_table.php',
            base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php')
        );
    }

    /**
     * Compiles the "HomeController" stub.
     *
     * @return string
     */
    protected static function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            Container::getInstance()->getNamespace(),
            file_get_contents(__DIR__.'/stubs/controllers/HomeController.stub')
        );
    }

    /**
     * Get full view path relative to the application's configured view path.
     *
     * @param  string  $path
     * @return string
     */
    protected static function getViewPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'), $path,
        ]);
    }
}
