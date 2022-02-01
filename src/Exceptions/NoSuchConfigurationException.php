<?php

namespace Innocenzi\Vite\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

final class NoSuchConfigurationException extends ViteException implements ProvidesSolution
{
    public function __construct(protected $configName)
    {
        $this->message = "Configuration \"${configName}\" does not exist.";
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create("Add it to your configuration")
            ->setSolutionDescription('That configuration should be defined in the `vite.configs` configuration option.')
            ->setDocumentationLinks([
                'Using multiple configurations' => 'https://laravel-vite.dev/guide/extra-topics/multiple-configurations',
                'Configuration' => 'https://laravel-vite.dev/guide/essentials/configuration',
            ]);
    }
}
