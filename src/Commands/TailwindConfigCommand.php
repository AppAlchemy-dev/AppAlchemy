<?php

namespace AppAlchemy\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TailwindConfigCommand extends Command
{
    protected $signature = 'appalchemy:tailwind-config';

    protected $description = 'Add AppAlchemy Tailwind CSS plugin to tailwind.config.js';

    public function handle(): int
    {
        $configPath = base_path('tailwind.config.js');

        if (! File::exists($configPath)) {
            $this->error('tailwind.config.js not found. Make sure Tailwind CSS is installed.');

            return 1;
        }

        $config = File::get($configPath);

        if (str_contains($config, 'appalchemy-app')) {
            $this->info('AppAlchemy plugin already exists in tailwind.config.js');

            return 0;
        }

        $plugin = <<<'EOT'
        plugin(function ({addVariant}) {
            addVariant('appalchemy-app', ['&.appalchemy-app', '.appalchemy-app &']);
        }),
        EOT;

        $config = preg_replace(
            '/(plugins\s*:\s*\[)/',
            "$1\n        $plugin",
            $config
        );

        File::put($configPath, $config);

        $this->info('AppAlchemy plugin added to tailwind.config.js successfully.');

        return 0;
    }
}
