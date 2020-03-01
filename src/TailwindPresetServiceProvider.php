<?php

namespace Nickfairchild\TailwindPreset;

use Laravel\Ui\UiCommand;
use Illuminate\Support\ServiceProvider;

class TailwindPresetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        UiCommand::macro('tailwind', function (UiCommand $command) {
            TailwindPreset::install();

            $command->info('Tailwind scaffolding installed successfully.');
            $command->comment('Please run "npm install && npm run dev" to compile your fresh scaffolding.');

            if ($command->option('auth')) {
                TailwindPreset::auth();

                $command->info('Authentication scaffolding generated successfully.');
            }
        });
    }
}
