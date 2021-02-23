<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class Vite
{
    protected ?Manifest $manifest;
    protected ?string $manifestPath;
    protected ?bool $isDevelopmentServerRunning;

    /**
     * Creates a new Vite instance.
     */
    public function __construct(string $manifestPath = null)
    {
        $this->manifestPath = $manifestPath;
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

        return $this->getEntry('@vite/client');
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

        return collect(\config('vite.entrypoints', []))
            ->map(fn ($directory) => \base_path($directory))
            ->filter(fn ($directory) => File::isDirectory($directory))
            ->flatMap(fn ($directory) => File::files($directory))
            ->filter(fn (SplFileInfo $file) => ! Str::endsWith($file->getFilename(), '.d.ts', ))
            ->map(fn (SplFileInfo $file) => $this->createDevelopmentScriptTag(
                Str::of($file->getPathname())
                    ->replace(\base_path(), '')
                    ->replace('\\', '/')
                    ->ltrim('/')
            ));
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
        if (! App::environment('local')) {
            return true;
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
            return $this->isDevelopmentServerRunning ??= Http::withOptions([
                'connect_timeout' => config('vite.ping_timeout'),
            ])->get(config('vite.dev_url') . '/@vite/client')->successful();
        } catch (\Throwable $th) {
        }

        return false;
    }

    protected function createDevelopmentScriptTag(string $path): Htmlable
    {
        // I suspect ASSET_URL should be takin into account here.
        // If you find out it does, feel free to open an issue.
        return new HtmlString(sprintf(
            '<script type="module" src="%s%s"></script>',
            Str::finish(\config('vite.dev_url'), '/'),
            $path
        ));
    }
}
