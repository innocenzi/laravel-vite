<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Vite
{
    protected ?Manifest $manifest;
    protected ?string $manifestPath;
    protected ?bool $isDevelopmentServerRunning;
    protected static ?bool $withoutManifest = false;

    /** @var callable */
    public static $generateTagsUsing = null;

    /**
     * Creates a new Vite instance.
     */
    public function __construct(string $manifestPath = null)
    {
        $this->manifestPath = $manifestPath;
    }

    /**
     * Defines a callback to generate manifest tags with.
     * First argument is the URL, second is whether it is a script or a style tag, third is the ManifestEntry instance.
     */
    public static function generateTagsUsing(callable $callable): void
    {
        static::$generateTagsUsing = $callable;
    }

    /**
     * Configures Vite to not use the manifest.
     */
    public static function withoutManifest(): void
    {
        static::$withoutManifest = true;
    }

    /**
     * Configures Vite to automatically determine if the manifest should be used.
     */
    public static function withManifest(): void
    {
        static::$withoutManifest = false;
    }

    /**
     * Adds a fallback route to redirect asset requests to Vite's development server.
     * @see https://laravel-vite.innocenzi.dev/guide/troubleshooting.html#imported-assets-don-t-load-in-the-local-environment
     *
     * @deprecated Since Vite 2.6-beta.3, this is no longer needed. https://github.com/vitejs/vite/pull/5104
     */
    public static function redirectAssets(): void
    {
        if (! App::environment('local')) {
            return;
        }

        Route::get('/resources/{path}', function (string $path) {
            return Redirect::to(config('vite.dev_url') . '/resources/' . $path);
        })->where('path', '.*');
    }

    /**
     * Returns the manifest, reading it from the disk if necessary.
     */
    public function getManifest(): ?Manifest
    {
        return $this->manifest ??= Manifest::read($this->manifestPath);
    }

    /**
     * Gets the script tag for the client module.
     */
    public function getClientScript(): Htmlable
    {
        if (! $this->isDevelopmentServerRunning()) {
            return new HtmlString();
        }

        return $this->createDevelopmentScriptTag('@vite/client');
    }

    /**
     * Gets the script tag for React's refresh runtime.
     */
    public function getReactRefreshRuntimeScript(): Htmlable
    {
        if (! $this->isDevelopmentServerRunning()) {
            return new HtmlString();
        }

        $url = config('vite.dev_url');

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
     * Gets an entry from the given name.
     */
    public function getEntry(string $name): Htmlable
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getEntry($name);
        }

        return $this->getEntries()->first(fn (Htmlable $entry) => Str::contains($entry->toHtml(), $name))
            ?? $this->createDevelopmentScriptTag($name);
    }

    /**
     * Gets every registered or automatic entry point.
     */
    public function getEntries(): Collection
    {
        if ($this->shouldUseManifest()) {
            return $this->getManifest()->getEntries();
        }

        return $this->findEntrypoints()
            ->map(fn (\SplFileInfo $file) => $this->createDevelopmentScriptTag(
                Str::of($file->getPathname())
                    ->replace(base_path(), '')
                    ->replace('\\', '/')
                    ->ltrim('/')
            ));
    }

    /**
     * Finds entrypoints from the configuration.
     */
    public function findEntrypoints(): Collection
    {
        if (! $entrypoints = config('vite.entrypoints', [])) {
            return collect();
        }

        return collect($entrypoints)
            ->flatMap(function (string $fileOrDirectory) {
                if (! file_exists($fileOrDirectory)) {
                    $fileOrDirectory = base_path($fileOrDirectory);
                }

                if (! file_exists($fileOrDirectory)) {
                    return [];
                }

                if (is_dir($fileOrDirectory)) {
                    return File::files($fileOrDirectory);
                }
                
                return [new \SplFileInfo($fileOrDirectory)];
            })
            ->unique(fn (\SplFileInfo $file) => $file->getPathname())
            ->filter(function (\SplFileInfo $file) {
                return ! collect(config('vite.ignore_patterns'))
                    ->some(fn ($pattern) => preg_match($pattern, $file->getFilename()));
            });
    }

    /**
     * Gets the script tags for the Vite client and the entrypoints.
     */
    public function getClientAndEntrypointTags(): Htmlable
    {
        $entries = collect();

        if (! $this->shouldUseManifest()) {
            $entries->push($this->getClientScript());
        }

        return new HtmlString(
            $entries->merge($this->getEntries())
                ->map(fn (Htmlable $entry) => $entry->toHtml())
                ->join('')
        );
    }

    /**
     * Checks if the manifest should be used to get an entry.
     */
    protected function shouldUseManifest(): bool
    {
        if (static::$withoutManifest === true) {
            return false;
        }

        if (! App::environment('local')) {
            return true;
        }

        if (! is_numeric(config('vite.ping_timeout'))) {
            return false;
        }

        if (! $this->isDevelopmentServerRunning()) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the development server is running.
     */
    public function isDevelopmentServerRunning(): bool
    {
        try {
            ['host' => $hostname, 'port' => $port] = parse_url(config('vite.ping_url') ?? config('vite.dev_url'));
            $connection = @fsockopen($hostname, $port, $errno, $errstr, config('vite.ping_timeout'));

            if (\is_resource($connection)) {
                fclose($connection);

                return true;
            }
        } catch (\Throwable $th) {
        }

        return false;
    }

    /**
     * Creates the script tag for including the development server.
     */
    protected function createDevelopmentScriptTag(string $path): Htmlable
    {
        // I suspect ASSET_URL should be takin into account here.
        // If you find out it does, feel free to open an issue.
        return new HtmlString(sprintf(
            '<script type="module" src="%s%s"></script>',
            Str::finish(config('vite.dev_url'), '/'),
            $path
        ));
    }

    /**
     * Gets a valid URL for the given asset. During development, the returned URL will be relative to the development server.
     */
    public function getAssetUrl(string $path): string
    {
        if ($this->shouldUseManifest()) {
            return asset(sprintf('/%s/%s', config('vite.build_path'), $path));
        }

        return sprintf('%s/%s', config('vite.dev_url'), $path);
    }
}
