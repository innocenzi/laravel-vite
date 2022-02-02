<?php

namespace Innocenzi\Vite\Exceptions;

class ViteException extends \Exception
{
    protected function hasConfigName(): bool
    {
        return $this->getConfigName() !== 'default';
    }

    protected function getConfigName(string $default = 'default'): string
    {
        if (property_exists($this, 'configName')) {
            return $this->configName ?? $default;
        }

        return $default;
    }
}
