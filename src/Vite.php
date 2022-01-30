<?php

namespace Innocenzi\Vite;

final class Vite
{
    const CLIENT_SCRIPT_PATH = '@vite/client';

    protected array $configs = [];

    /**
     * Gets the given configuration or the default one.
     */
    public function config(string $name = null): Configuration
    {
        $name ??= config('vite.default');

        return $this->configs[$name] ??= new Configuration($name);
    }
    
    /**
     * Execute a method against the default configuration.
     */
    public function __call($method, $parameters)
    {
        return $this->config()->{$method}(...$parameters);
    }
}
