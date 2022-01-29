<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class UnknownConfigurationException extends ViteException implements ProvidesSolution
{
    public function __construct(protected $name)
    {
        $this->message = "Configuration \"${name}\" does not exist.";
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Add it to your configuration")
            ->setSolutionDescription('That configuration should be defined in the `vite.configs` configuration option.')
            ->setDocumentationLinks([
                'About configurations' => 'https://laravel-vite.innocenzi.dev/guide/configurations.html',
            ]);
    }
}
