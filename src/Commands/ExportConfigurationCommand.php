<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;

class ExportConfigurationCommand extends Command
{
    public $signature = 'vite:config {--export= : Path to a file to write the configuration into.}';
    public $description = 'Prints the Vite configuration.';
    public $hidden = true;

    public function __construct(protected EntrypointsFinder $entrypointsFinder)
    {
        parent::__construct();
    }

    public function handle()
    {
        $config = $this->getConfigurationAsJson();

        if ($path = $this->option('export')) {
            File::put($path, $config);

            $this->info("Configuration file written to <comment>${path}</comment>.");

            return;
        }

        $this->output->write($config);
    }

    public function getConfigurationAsJson()
    {
        $config = config('vite');

        foreach ($config['configs'] as $name => $value) {
            $config['configs'][$name]['entrypoints']['paths'] = $this->entrypointsFinder->find(
                $value['entrypoints']['paths'] ?? [],
                $value['entrypoints']['ignore'] ?? []
            )->map->getPathname();
        }

        return json_encode($config);
    }
}
