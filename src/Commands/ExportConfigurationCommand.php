<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Innocenzi\Vite\Vite;

class ExportConfigurationCommand extends Command
{
    public $signature = 'vite:config {--export= : Path to a file to write the configuration into.}';
    public $description = 'Prints the Vite configuration.';
    public $hidden = true;

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

    public function getConfigurationAsJson(): string
    {
        $entrypoints = app(Vite::class)->findEntrypoints()
            ->map(fn (\SplFileInfo $file) => Str::of($file->getPathname())
            ->replace(base_path(), '')
            ->replace('\\', '/')
            ->ltrim('/'))
            ->values();

        return json_encode(array_merge(config('vite'), [
            'entrypoints' => $entrypoints,
        ]));
    }
}
