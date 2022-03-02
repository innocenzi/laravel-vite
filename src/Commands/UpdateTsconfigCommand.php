<?php

namespace Innocenzi\Vite\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\File;

class UpdateTsconfigCommand extends Command
{
    use ConfirmableTrait;

    public $signature = 'vite:tsconfig {--force : Force the operation to run when in production}';
    public $description = 'Update the tsconfig.json file according to the current Vite configuration.';

    public function handle()
    {
        if (!$this->confirmToProceed()) {
            return self::SUCCESS;
        }

        if (!File::exists($this->getTsConfigPath())) {
            $this->createTsConfig();
        }

        if (!config('vite.aliases')) {
            return;
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
        $raw = File::get($this->getTsConfigPath());

        if (!$tsconfig = json_decode($raw, true)) {
            throw new \RuntimeException('Unable to parse the tsconfig.json file.');
        }

        $tsconfig['compilerOptions']['baseUrl'] = '.';
        $tsconfig['compilerOptions']['paths'] = collect(config('vite.aliases'))
            ->mapWithKeys(fn ($value, $key) => ["${key}/*" => ["${value}/*"]])
            ->merge($tsconfig['compilerOptions']['paths'] ?? [])
            ->toArray();

        $indent = $this->detectIndent($raw);
        $json = preg_replace_callback(
            '/^ +/m',
            fn ($m) => str_repeat($indent, \strlen($m[0]) / 4),
            json_encode($tsconfig, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)
        );

        File::put($this->getTsConfigPath(), $json);
    }

    protected function detectIndent(string $raw): string
    {
        return rescue(fn () => explode('"', explode("\n", $raw)[1])[0], report: false) ?? '    ';
    }

    protected function getTsConfigPath(): string
    {
        return base_path('tsconfig.json');
    }
}
