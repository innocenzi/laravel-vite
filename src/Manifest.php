<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;

class Manifest implements Htmlable
{
    const MANIFEST_FILE_NAME = 'manifest.json';

    protected Collection $rawEntries;
    protected Collection $entries;

    public function __construct(string $path = null)
    {
        $this->rawEntries = Collection::make(\json_decode(\file_get_contents($this->getManifestPath($path)), true));
        $this->entries = $this->rawEntries
            ->map(fn (array $value) => ManifestEntry::fromArray($value))
            ->filter(fn (ManifestEntry $entry) => $entry->isEntry);

        if (App::environment('local')) {
            $this->entries->prepend(ManifestEntry::client(), 'client');
        }
    }

    /**
     * Reads the manifest file and returns its representation.
     */
    public static function read(string $path = null): Manifest
    {
        return new Manifest($path);
    }

    /**
     * Gets the script tag for the client module.
     */
    public function getClientScript(): string
    {
        return \sprintf('<script type="module" src="%s/@vite/client"></script>', \config('vite.hmr_url'));
    }

    /**
     * Gets the manifest entry for the given name.
     */
    public function getEntry(string $name): ManifestEntry | Htmlable
    {
        // TODO: clean up
        return tap(new HtmlString($this->entries->get($name, '')), function (HtmlString $entry) use ($name) {
            if ($entry->isEmpty() && $name !== 'client') {
                throw new \Exception("Entry point '${name}' does not exist.");
            }
        });
    }

    /**
     * Gets every entry.
     *
     * @return Collection<ManifestEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }
    
    /**
     * Gets the path to the manifest file.
     */
    protected function getManifestPath(string $path = null): string
    {
        $path ??= \config('vite.build_path') . '/' . self::MANIFEST_FILE_NAME;

        if (! \file_exists($path)) {
            throw new \LogicException(\sprintf('%s not found (looked for %s).', self::MANIFEST_FILE_NAME, $path));
        }

        return $path;
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->entries->map
            ->toHtml()
            ->join('');
    }

    public function __toString()
    {
        return $this->toHtml();
    }
}
