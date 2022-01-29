<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class ManifestNotFoundException extends ViteException implements ProvidesSolution
{
    protected array $links = [
        'Using the development server' => 'https://laravel-vite.innocenzi.dev/guide/usage.html#development',
        'Building the assets' => 'https://laravel-vite.innocenzi.dev/guide/production.html',
    ];

    public function __construct(
        protected string $manifestPath,
        protected string $configName
    ) {
        $this->message = $configName === 'default'
            ? "The manifest could not be found."
            : "The manifest for the \"{$configName}\" configuration could not be found.";
    }

    public function getSolution(): Solution
    {
        $baseCommand = collect([
            'pnpm-lock.yaml' => 'pnpm',
            'yarn.lock' => 'yarn',
        ])->reduce(function ($default, $command, $lockFile) {
            if (file_exists(base_path($lockFile))) {
                return $command;
            }

            return $default;
        }, 'npm run');

        return app()->environment('local')
            ? $this->getLocalSolution($baseCommand)
            : $this->getProductionSolution($baseCommand);
    }

    protected function getLocalSolution(string $baseCommand): Solution
    {
        return BaseSolution::create('Start the development server')
            ->setSolutionDescription("Run `{$this->getCommand($baseCommand, 'dev')}` in your terminal and refresh the page.")
            ->setDocumentationLinks($this->links);
    }

    protected function getProductionSolution(string $baseCommand): Solution
    {
        return BaseSolution::create('Build the production assets')
            ->setSolutionDescription("Run `{$this->getCommand($baseCommand, 'build')}` in your terminal and refresh the page.")
            ->setDocumentationLinks($this->links);
    }

    protected function getCommand(string $baseCommand, string $type): string
    {
        $command = "${baseCommand} ${type}";
    
        if ($this->configName !== 'default') {
            $command .= " --config vite.{$this->configName}.config.ts";
        }

        return $command;
    }
}
