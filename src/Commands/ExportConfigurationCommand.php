<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Innocenzi\Vite\Vite;

class ExportConfigurationCommand extends Command
{
    public $signature = 'vite:config';
    public $description = 'Prints the Vite configuration.';
    public $hidden = true;

    public function handle()
    {
        $this->output->write($this->getConfigurationAsJson());
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
