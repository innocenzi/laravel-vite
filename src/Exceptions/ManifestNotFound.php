<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class ManifestNotFound extends ViteException implements ProvidesSolution
{
    protected string $manifestPath;
    protected array $links = [
        'Using the development server' => 'https://laravel-vite.innocenzi.dev/guide/usage.html#development',
        'Building the assets' => 'https://laravel-vite.innocenzi.dev/guide/production.html',
    ];

    public function __construct(string $path)
    {
        $this->manifestPath = $path;
        $this->message = sprintf('The manifest could not be found.');
    }

    public function getSolution(): Solution
    {
        $baseCommand = collect([
            'pnpm-lock.yaml' => 'pnpm',
            'yarn.lock' => 'yarn',
        ])->reduce(function ($default, $command, $lockFile) {
            if (File::exists(base_path($lockFile))) {
                return $command;
            }

            return $default;
        }, 'npm run');

        return App::environment('local')
            ? $this->getLocalSolution($baseCommand)
            : $this->getProductionSolution($baseCommand);
    }

    protected function getLocalSolution(string $baseCommand = 'npm run'): Solution
    {
        return BaseSolution::create('Start the development server')
            ->setSolutionDescription("Run `${baseCommand} dev` in your terminal and refresh the page.")
            ->setDocumentationLinks($this->links);
    }

    protected function getProductionSolution(string $baseCommand = 'npm run'): Solution
    {
        return BaseSolution::create('Build the production assets')
            ->setSolutionDescription("Run `${baseCommand} build` in your terminal and refresh the page.")
            ->setDocumentationLinks($this->links);
    }
}
