<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class Manifest implements Htmlable
{
    const MANIFEST_FILE_NAME = 'manifest.json';

    protected Collection $rawEntries;
    protected Collection $entries;

    /**
     * Reads the manifest file and returns its representation.
     */
    public static function read(string $path = null): Manifest
    {
        return new Manifest($path);
    }

    /**
     * Creates a Manifest instance.
     *
     * @param string $path Absolute path to the manifest
     */
    public function __construct(string $path = null)
    {
        $this->rawEntries = Collection::make(\json_decode(\file_get_contents($this->getManifestPath($path)), true));
        $this->entries = $this->rawEntries
            ->map(fn (array $value) => ManifestEntry::fromArray($value))
            ->filter(fn (ManifestEntry $entry) => $entry->isEntry);
    }

    /**
     * Gets the manifest entry for the given name.
     */
    public function getEntry(string $entry): ?ManifestEntry
    {
        return $this->entries->get(
            $entry,
            fn () => throw new \Exception("${entry} does not exist in the manifest. Make sure you added an entry point in the Vite configuration.")
        );
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
        $path ??= \public_path(\config('vite.build_path') . '/' . self::MANIFEST_FILE_NAME);

        if (! \file_exists($path)) {
            throw new \LogicException(\sprintf('The manifest could not be found. Did you run the build command? Tried: %s', $path));
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
