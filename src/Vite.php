<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class Vite
{
    protected ?Manifest $manifest;

    public function __construct(string $manifest = null)
    {
        if (! App::environment('local')) {
            $this->manifest = Manifest::read($manifest);
        }
    }

    /**
     * Gets the script tag for the client module.
     */
    public function getClientScript(): Htmlable
    {
        if (! App::environment('local')) {
            return new HtmlString();
        }

        return $this->getEntry('@vite/client');
    }

    /**
     * Gets the given entry.
     */
    public function getEntry(string $name): Htmlable
    {
        if (! App::environment('local')) {
            return $this->manifest->getEntry($name);
        }

        return $this->getEntries()->first(fn (Htmlable $entry) => Str::contains($entry->toHtml(), $name))
            ?? $this->createDevelopmentScriptTag($name);
    }

    /**
     * Gets every registered or automatic entry point.
     */
    public function getEntries(): Collection
    {
        if (! App::environment('local')) {
            return $this->manifest->getEntries();
        }

        return collect(\config('vite.entrypoints', []))
            ->map(fn ($directory) => \base_path($directory))
            ->filter(fn ($directory) => File::isDirectory($directory))
            ->flatMap(fn ($directory) => File::files($directory))
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

        if (App::environment('local')) {
            $entries->push($this->getClientScript());
        }

        return new HtmlString(
            $entries->merge($this->getEntries())
                ->map(fn (Htmlable $entry) => $entry->toHtml())
                ->join('')
        );
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
