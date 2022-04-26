<?php

namespace Innocenzi\Vite;

use Closure;
use Illuminate\Support\Traits\Macroable;

final class Vite
{
    use Macroable;
    
    const CLIENT_SCRIPT_PATH = '@vite/client';

    protected array $configs = [];

    /**
     * @var (Closure(string, Innocenzi\Vite\Chunk|null): string)
     */
    public static Closure|null $makeScriptTagsCallback = null;
    
    /**
     * @var (Closure(string, Innocenzi\Vite\Chunk|null): string)
     */
    public static Closure|null $makeStyleTagsCallback = null;

    /**
     * @var (Closure(Innocenzi\Vite\Configuration): bool|null)
     */
    public static Closure|null $useManifestCallback = null;

    /**
     * Gets the given configuration or the default one.
     */
    public function config(string $name = null): Configuration
    {
        $name ??= config('vite.default');

        return $this->configs[$name] ??= new Configuration($name);
    }

    /**
     * Sets the logic for creating a script tag.
     *
     * @param (Closure(string, Innocenzi\Vite\Chunk|null): string) $callback
     */
    public static function makeScriptTagsUsing(Closure $callback = null): void
    {
        static::$makeScriptTagsCallback = $callback;
    }

    /**
     * Sets the logic for creating a style tag.
     *
     * @param (Closure(string, Innocenzi\Vite\Chunk|null): string) $callback
     */
    public static function makeStyleTagsUsing(Closure $callback = null): void
    {
        static::$makeStyleTagsCallback = $callback;
    }

    /**
     * Sets the logic for determining if the manifest should be used.
     *
     * @param (Closure(Innocenzi\Vite\Configuration): bool|null) $callback
     */
    public static function useManifest(Closure $callback = null): void
    {
        static::$useManifestCallback = $callback;
    }
    
    /**
     * Execute a method against the default configuration.
     */
    public function __call($method, $parameters)
    {
        return $this->config()->{$method}(...$parameters);
    }
}
