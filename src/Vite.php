<?php

namespace Innocenzi\Vite;

use Closure;

final class Vite
{
    const CLIENT_SCRIPT_PATH = '@vite/client';

    protected array $configs = [];

    /**
     * @var (Closure(string): string)
     */
    public static Closure $makeScriptTagsCallback;
    
    /**
     * @var (Closure(string): string)
     */
    public static Closure $makeStyleTagsCallback;

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
     * Sets the logic for creating a script tag.
     *
     * @param (Closure(string): string) $callback
     */
    public static function makeScriptTagsUsing(Closure $callback): void
    {
        static::$makeScriptTagsCallback = $callback;
    }

    /**
     * Sets the logic for creating a style tag.
     *
     * @param (Closure(string): string) $callback
     */
    public static function makeStyleTagsUsing(Closure $callback): void
    {
        static::$makeStyleTagsCallback = $callback;
    }
    
    /**
     * Execute a method against the default configuration.
     */
    public function __call($method, $parameters)
    {
        return $this->config()->{$method}(...$parameters);
    }
}
