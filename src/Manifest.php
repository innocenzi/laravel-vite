<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Innocenzi\Vite\Exceptions\NoSuchEntrypointException;

final class Manifest implements Htmlable
{
    protected Collection $chunks;
    protected Collection $entries;

    /**
     * Creates a Manifest instance.
     *
     * @param string $path Absolute path to the manifest
     */
    public function __construct(protected string $path)
    {
        $this->chunks = Collection::make(json_decode(file_get_contents($path), true));
        $this->entries = $this->chunks
            ->map(fn (array $value) => Chunk::fromArray($this, $value))
            ->filter(fn (Chunk $entry) => $entry->isEntry);
    }

    /**
     * Reads the manifest file and returns its representation.
     */
    public static function read(string $path): Manifest
    {
        return new Manifest($path);
    }

    /**
     * Gets the absolute path of this manifest.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Gets the manifest entry for the given name.
     */
    public function getEntry(string $name): Chunk
    {
        if (! $entry = $this->entries->first(fn (Chunk $entry) => str_contains($entry->src, $name))) {
            $configName = basename(\dirname($this->getPath()));

            throw new NoSuchEntrypointException($name, $configName);
        }

        return $entry;
    }

    /**
     * Gets every entry.
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    /**
     * Gets every chunk.
     */
    public function getChunks(): Collection
    {
        return $this->chunks;
    }

    public function toHtml()
    {
        return $this->entries->map->toHtml()->join('');
    }
}
