<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAliasesCommand extends Command
{
    public $signature = 'vite:aliases';
    public $description = 'Writes the configured aliases to the tsconfig.json file.';

    public function handle()
    {
        if (false === config('vite.aliases')) {
            return;
        }

        if (! File::exists($this->getTsConfigPath())) {
            $this->createTsConfig();
        }

        $this->writeAliases();
        $this->output->success('The tsconfig.json file has been updated.');
    }

    protected function createTsConfig(): void
    {
        File::put($this->getTsConfigPath(), json_encode([
            'compilerOptions' => [
                'target' => 'esnext',
                'module' => 'esnext',
                'moduleResolution' => 'node',
                'strict' => true,
                'jsx' => 'preserve',
                'sourceMap' => true,
                'resolveJsonModule' => true,
                'esModuleInterop' => true,
                'lib' => ['esnext', 'dom'],
                'types' => ['vite/client'],
            ],
            'include' => ['resources/**/*'],
        ], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));
    }

    protected function writeAliases(): void
    {
        $tsconfig = json_decode(File::get($this->getTsConfigPath()), true);

        if (! $tsconfig) {
            throw new \RuntimeException('Unable to parse the tsconfig.json file.');
        }

        $tsconfig['compilerOptions']['baseUrl'] = '.';
        $tsconfig['compilerOptions']['paths'] = collect(config('vite.aliases'))
            ->mapWithKeys(fn ($value, $key) => ["${key}/*" => ["${value}/*"]])
            ->merge($tsconfig['compilerOptions']['paths'] ?? [])
            ->toArray();

        File::put($this->getTsConfigPath(), json_encode($tsconfig, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES));
    }

    protected function getTsConfigPath(): string
    {
        return base_path('tsconfig.json');
    }
}
