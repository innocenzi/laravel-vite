<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class NoSuchEntrypointException extends ViteException implements ProvidesSolution
{
    protected $entry;

    public function __construct($entry)
    {
        $this->entry = (string) $entry;
        $this->message = "Entry '{$this->entry}' does not exist in the manifest.";
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Add it to your configuration")
            ->setSolutionDescription('That entry point should be defined by the `vite.entrypoints` configuration option.')
            ->setDocumentationLinks([
                'About entrypoints' => 'https://laravel-vite.innocenzi.dev/guide/usage.html#entrypoints',
                'Configuring entrypoints' => 'https://laravel-vite.innocenzi.dev/guide/configuration.html#options',
            ]);
    }
}
