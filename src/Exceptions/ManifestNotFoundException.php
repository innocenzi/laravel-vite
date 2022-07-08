<?php

namespace Innocenzi\Vite\Exceptions;

use Spatie\Ignition\Contracts\BaseSolution;
use Spatie\Ignition\Contracts\ProvidesSolution;
use Spatie\Ignition\Contracts\Solution;

final class ManifestNotFoundException extends ViteException implements ProvidesSolution
{
    protected array $links = [
        'About development' => 'https://laravel-vite.dev/guide/essentials/development',
        'Building for production' => 'https://laravel-vite.dev/guide/essentials/building-for-production',
    ];

    public function __construct(
        protected string $manifestPath,
        protected ?string $configName = null
    ) {
        $this->message = !$this->hasConfigName()
            ? "The manifest could not be found."
            : "The manifest for the \"{$this->getConfigName()}\" configuration could not be found.";
    }

    public function getSolution(): Solution
    {
        /** @var string */
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
    
        if ($this->hasConfigName()) {
            $command .= " --config vite.{$this->getConfigName()}.config.ts";
        }

        return $command;
    }
}
