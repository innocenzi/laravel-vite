<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;
use Innocenzi\Vite\Exceptions\ManifestNotFound;
use Innocenzi\Vite\Exceptions\NoSuchConfigurationException;
use Innocenzi\Vite\ServerCheckers\ServerChecker;
use Innocenzi\Vite\TagGenerators\TagGenerator;

final class Configuration
{
    public function __construct(
        protected string $name,
        protected ?Manifest $manifest = null,
        protected ?EntrypointsFinder $entrypointsFinder = null,
        protected ?ServerChecker $serverChecker = null,
        protected ?TagGenerator $tagGenerator = null,
    ) {
        if (! config()->has("vite.configs.${name}")) {
            throw new NoSuchConfigurationException($name);
        }

        $this->entrypointsFinder ??= app(EntrypointsFinder::class);
        $this->serverChecker ??= app(ServerChecker::class);
        $this->tagGenerator ??= app(TagGenerator::class);
    }
    
    /**
     * Returns the manifest, reading it from the disk if necessary.
     */
    public function getManifest(): ?Manifest
    {
        $path = public_path(sprintf('%s/%s', $this->config('build_path'), 'manifest.json'));

        if (! file_exists($path)) {
            throw new ManifestNotFound($path, $this->name);
        }

        return $this->manifest ??= Manifest::read($path);
    }

    /**
     * Gets the tag for the given entry.
     */
    public function getTag(string $entryName): Htmlable
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getEntry($entryName);
        }

        return $this->getEntries()->first(fn (Htmlable $chunk) => str_contains($chunk->toHtml(), $entryName))
            ?? $this->createDevelopmentTag($entryName);
    }

    /**
     * Gets every chunk.
     */
    public function getEntries(): Collection
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getEntries();
        }

        return $this->findEntrypoints()
            ->map(fn (\SplFileInfo $file) => $this->createDevelopmentTag(
                Str::of($file->getPathname())
                    ->replace(base_path(), '')
                    ->replace('\\', '/')
                    ->ltrim('/'),
            ));
    }

    /**
     * Gets all tags for this configuration.
     */
    public function getTags(): Htmlable
    {
        $tags = collect();

        if (! $this->shouldUseManifest()) {
            $tags->push($this->getClientScriptTag());
        }

        $tags = $tags->merge($this->getEntries())
            ->map(fn (Htmlable $chunk) => $chunk->toHtml())
            ->join('');

        return new HtmlString($tags);
    }
    
    /**
     * Gets the script tag for the client module.
     */
    public function getClientScriptTag(): Htmlable
    {
        if ($this->shouldUseManifest()) {
            return new HtmlString();
        }

        return $this->createDevelopmentTag('@vite/client');
    }

    /**
     * Gets the script tag for React's refresh runtime.
     */
    public function getReactRefreshRuntimeScript(): Htmlable
    {
        if ($this->shouldUseManifest()) {
            return new HtmlString();
        }

        $url = $this->config('dev_server.url');

        $script = <<<HTML
            <script type="module">
                import RefreshRuntime from "{$url}/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.\$RefreshReg$ = () => {}
                window.\$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>
        HTML;

        return new HtmlString($script);
    }

    /**
     * Gets a valid URL for the given asset.
     * During development, the development server's URL will be used.
     */
    public function getAssetUrl(string $path): string
    {
        if ($this->shouldUseManifest()) {
            return asset(sprintf('/%s/%s', $this->config('build_path'), $path));
        }

        return sprintf('%s/%s', $this->config('dev_server.url'), $path);
    }

    /**
     * Finds entrypoints from the configuration.
     */
    protected function findEntrypoints(): Collection
    {
        $paths = $this->config('entrypoints.paths', []);
        $ignore = $this->config('entrypoints.ignore', []);

        return $this->entrypointsFinder->find($paths, $ignore);
    }

    /**
     * Checks if the manifest should be used to get an entry.
     */
    protected function shouldUseManifest(): bool
    {
        // If the development server is disabled, use the manifest.
        if (! $this->config('dev_server.enabled', true)) {
            return true;
        }

        // If disabled in tests via the configuration, do not use the manifest.
        if (app()->environment('testing') && ! config('vite.testing.use_manifest', false)) {
            return false;
        }

        // If running in production, do use the manifest.
        if (! app()->environment('local')) {
            return true;
        }

        // At this point, environment checks have passed, so we're likely to not
        // use the manifest. If the ping is disabled, do not use the manifest.
        if (! $this->config('dev_server.ping_before_using_manifest', true)) {
            return false;
        }

        // If we wanted to check if the dev server was running but it
        // is not, actually use the manifest.
        if (! $this->isDevelopmentServerRunning()) {
            return true;
        }

        // Otherwise, the manifest should not be used.
        return false;
    }

    /**
     * Creates a script tag using the development server URL.
     */
    protected function createDevelopmentTag(string $path): Htmlable
    {
        $url = Str::of($this->config('dev_server.url'))->finish('/')->append($path);

        if (Str::endsWith($path, '.css')) {
            return new HtmlString($this->tagGenerator->makeStyleTag($url));
        }

        return new HtmlString($this->tagGenerator->makeScriptTag($url));
    }

    /**
     * Checks if the development server is running.
     */
    protected function isDevelopmentServerRunning(): bool
    {
        $url = $this->config('dev_server.ping_url') ?? $this->config('dev_server.url');
        $timeout = $this->config('dev_server.ping_timeout');

        return $this->serverChecker->ping($url, $timeout);
    }

    /**
     * Gets an option value for this specific Vite configuration.
     */
    protected function config(mixed $value, mixed $default = null): mixed
    {
        return config("vite.configs.{$this->name}.{$value}", $default);
    }
}
