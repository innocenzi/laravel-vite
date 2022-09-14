<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Innocenzi\Vite\EntrypointsFinder\EntrypointsFinder;
use Innocenzi\Vite\Exceptions\NoBuildPathException;
use Innocenzi\Vite\Exceptions\NoSuchConfigurationException;
use Innocenzi\Vite\Exceptions\NoSuchEntrypointException;
use Innocenzi\Vite\HeartbeatCheckers\HeartbeatChecker;
use Innocenzi\Vite\TagGenerators\TagGenerator;
use SplFileInfo;

final class Configuration
{
    use Macroable;

    public function __construct(
        protected string $name,
        protected ?Manifest $manifest = null,
        protected ?EntrypointsFinder $entrypointsFinder = null,
        protected ?HeartbeatChecker $heartbeatChecker = null,
        protected ?TagGenerator $tagGenerator = null,
    ) {
        if (!config()->has("vite.configs.${name}")) {
            throw new NoSuchConfigurationException($name);
        }

        $this->entrypointsFinder ??= app(EntrypointsFinder::class);
        $this->heartbeatChecker ??= app(HeartbeatChecker::class);
        $this->tagGenerator ??= app(TagGenerator::class);
    }

    /**
     * Returns the manifest, reading it from the disk if necessary.
     *
     * @throws NoBuildPathException
     */
    public function getManifest(): ?Manifest
    {
        if (!$this->config('build_path')) {
            throw new NoBuildPathException($this->name);
        }

        return $this->manifest ??= Manifest::read($this->getManifestPath());
    }

    /**
     * Returns the manifest path.
     */
    public function getManifestPath(): string
    {
        // If there is a strategy override, try to use that.
        if (\is_callable(Vite::$findManifestPathWith)) {
            $result = \call_user_func(Vite::$findManifestPathWith, $this);

            // Only override if there is a result.
            if (!\is_null($result)) {
                return $result;
            }
        }

        if (str_starts_with($this->config('build_path'), 'http')) {
            return sprintf('%s/%s', trim($this->config('build_path'), '/\\'), 'manifest.json');
        }

        return str_replace(
            ['\\', '//'],
            '/',
            public_path(sprintf('%s/%s', trim($this->config('build_path'), '/\\'), 'manifest.json'))
        );
    }

    /**
     * Returns the manifest's md5.
     */
    public function getHash(): string|null
    {
        $path = $this->getManifestPath();

        if (str_starts_with($path, 'http')) {
            return md5(Manifest::getManifestContent($path));
        }
        
        if (!file_exists($path)) {
            return null;
        }

        return md5_file($path) ?: null;
    }

    /**
     * Gets the tag for the given entry.
     */
    public function getTag(string $entryName): string
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getEntry($entryName);
        }

        return $this->getEntries()->first(fn (string $chunk) => str_contains($chunk, $entryName))
            ?? throw NoSuchEntrypointException::inConfiguration($entryName, $this->getName());
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
            ->map(fn (\SplFileInfo $file) => $this->createDevelopmentTag($this->normalizePathName($file)));
    }

    /**
     * Gets all tags for this configuration.
     */
    public function getTags(): string
    {
        $tags = collect();

        if (!$this->shouldUseManifest()) {
            $tags->push($this->getClientScriptTag());
        }

        return $tags->merge($this->getEntries())
            ->map(fn ($entrypoint) => (string) $entrypoint)
            ->join('');
    }

    /**
     * Gets the script tag for the client module.
     */
    public function getClientScriptTag(): string
    {
        if ($this->shouldUseManifest()) {
            return '';
        }

        return $this->createDevelopmentTag(Vite::CLIENT_SCRIPT_PATH);
    }

    /**
     * Gets the script tag for React's refresh runtime.
     */
    public function getReactRefreshRuntimeScript(): string
    {
        if ($this->shouldUseManifest()) {
            return '';
        }

        $script = <<<HTML
            <script type="module">
                import RefreshRuntime from "%s/@react-refresh"
                RefreshRuntime.injectIntoGlobalHook(window)
                window.\$RefreshReg$ = () => {}
                window.\$RefreshSig$ = () => (type) => type
                window.__vite_plugin_react_preamble_installed__ = true
            </script>
        HTML;

        return sprintf($script, $this->config('dev_server.url'));
    }

    /**
     * Gets an URL for the given entry.
     */
    public function getEntryUrl(string $entryName): string
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getEntry($entryName)->getAssetUrl();
        }

        try {
            return $this->findEntrypoints()
                ->map(fn (\SplFileInfo $file) => $this->getDevServerPathUrl($this->normalizePathName($file)))
                ->firstOrfail(fn (string $chunk) => str_contains($chunk, $entryName));
        } catch (\Throwable) {
            throw NoSuchEntrypointException::inConfiguration($entryName, $this->getName());
        }
    }

    /**
     * Gets a valid URL for the given asset.
     * During development, the development server's URL will be used.
     */
    public function getAssetUrl(string $path): string
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getChunk($path)->getAssetUrl();
        }

        return sprintf('%s/%s', rtrim($this->config('dev_server.url'), '/'), ltrim($path, '/'));
    }

    /**
     * Gets a configuration value.
     */
    public function getConfig(string $key = null): mixed
    {
        return $this->config($key);
    }

    /**
     * Gets the name of this configuration.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Checks whether this configuration currently uses the manifest.
     */
    public function usesManifest(): bool
    {
        return $this->shouldUseManifest();
    }

    /**
     * Checks whether this configuration currently uses the dev server.
     */
    public function usesServer(): bool
    {
        return !$this->usesManifest();
    }

    /**
     * Checks whether the manifest or development server is accessible.
     */
    public function canAccessAssets(): bool
    {
        if ($this->shouldUseManifest()) {
            try {
                return !!$this->getManifest();
            } catch (\Throwable) {
                return false;
            }
        }

        return $this->isDevelopmentServerRunning();
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
        // If there is a strategy override, try to use that.
        if (\is_callable(Vite::$useManifestCallback)) {
            $result = \call_user_func(Vite::$useManifestCallback, $this);

            // Only override if the result is a boolean.
            if (!\is_null($result)) {
                return $result;
            }
        }

        // If the development server is disabled, use the manifest.
        if (!$this->config('dev_server.enabled', true)) {
            return true;
        }

        // If disabled in tests via the configuration, do not use the manifest.
        if (app()->environment('testing') && !config('vite.testing.use_manifest', false)) {
            return false;
        }

        // If running in production, do use the manifest.
        if (!app()->environment('local')) {
            return true;
        }

        // At this point, environment checks have passed, so we're likely to not
        // use the manifest. If the ping is disabled, do not use the manifest.
        if (!$this->config('dev_server.ping_before_using_manifest', true)) {
            return false;
        }

        // If we wanted to check if the dev server was running but it
        // is not, actually use the manifest.
        if (!$this->isDevelopmentServerRunning()) {
            return true;
        }

        // Otherwise, the manifest should not be used.
        return false;
    }

    /**
     * Gets the development server URL for the given path.
     */
    protected function getDevServerPathUrl(string $path): string
    {
        return Str::of($this->config('dev_server.url'))->finish('/')->append($path);
    }

    /**
     * Gets the client development server URL for the given path.
     */
    protected function getClientDevServerPathUrl(string $path): string
    {
        if (!$this->config('dev_server.client_url')) {
            return $this->getDevServerPathUrl($path);
        }

        return Str::of($this->config('dev_server.client_url'))->finish('/')->append($path);
    }

    /**
     * Creates a script tag using the development server URL.
     */
    protected function createDevelopmentTag(string $path): string
    {
        $url = $this->getClientDevServerPathUrl($path);

        if (Str::endsWith($path, ['.css', '.scss', '.sass', '.less', '.styl', '.stylus'])) {
            return $this->tagGenerator->makeStyleTag($url);
        }

        return $this->tagGenerator->makeScriptTag($url);
    }

    /**
     * Checks if the development server is running.
     */
    protected function isDevelopmentServerRunning(): bool
    {
        $url = $this->config('dev_server.ping_url') ?? $this->config('dev_server.url');
        $timeout = $this->config('dev_server.ping_timeout');

        return $this->heartbeatChecker->ping($url, $timeout);
    }

    /**
     * Gets an option value for this specific Vite configuration.
     */
    protected function config(mixed $key = null, mixed $default = null): mixed
    {
        if ($key) {
            return config("vite.configs.{$this->name}.{$key}", $default);
        }

        return config("vite.configs.{$this->name}", $default);
    }

    /**
     * Gets the normalized path name for the given file.
     */
    protected function normalizePathName(SplFileInfo $file): string
    {
        return (string) Str::of($file->getPathname())
            ->replace(base_path(), '')
            ->replace('\\', '/')
            ->ltrim('/');
    }
}
