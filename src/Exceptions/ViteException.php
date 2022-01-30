<?php

namespace Innocenzi\Vite\Exceptions;

class ViteException extends \Exception
{
    protected function hasConfigName(): bool
    {
        return property_exists($this, 'configName') && $this->configName !== 'default';
    }

    protected function getConfigName(string $default = 'default'): string
    {
        if (property_exists($this, 'configName')) {
            return $this->configName;
        }

        return $default;
    }
}
