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
     * Sets whether the manifest should be used when testing.
     */
    public static function useManifest(bool $useManifest = true): void
    {
        config()->set('vite.testing.use_manifest', $useManifest);
    }
    
    /**
     * Execute a method against the default configuration.
     */
    public function __call($method, $parameters)
    {
        return $this->config()->{$method}(...$parameters);
    }
}
