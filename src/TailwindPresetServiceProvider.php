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
            $command->comment('Please run "yarn && yarn dev" to compile your fresh scaffolding.');

            if ($command->option('auth')) {
                $command->callSilent('ui:controllers');

                TailwindPreset::auth();

                $command->info('Authentication scaffolding generated successfully.');
            }

            if ($options = $command->option('option')) {
                foreach ($options as $option) {
                    if ($option === 'vue') {
                        VuePreset::install();

                        $command->info('Vue scaffolding generated successfully.');
                        $command->comment('Please run "yarn && yarn dev" to compile your fresh scaffolding.');
                    }
                }
            }
        });
    }
}
