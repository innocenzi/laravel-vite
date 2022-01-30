<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class NoSuchEntrypointException extends ViteException implements ProvidesSolution
{
    public function __construct(
        protected string $entry,
        protected ?string $configName = null
    ) {
        $this->message = "Entry \"${entry}\" does not exist in the manifest.";
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Add it to your configuration")
            ->setSolutionDescription("That entry point should be defined by the `vite.configs.{$this->getConfigName()}.entrypoints` configuration option.")
            ->setDocumentationLinks([
                'About entrypoints' => 'https://laravel-vite.innocenzi.dev/guide/usage.html#entrypoints',
                'Configuring entrypoints' => 'https://laravel-vite.innocenzi.dev/guide/configuration.html#options',
            ]);
    }
}
