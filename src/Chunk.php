<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Innocenzi\Vite\TagGenerators\TagGenerator;
use Stringable;

final class Chunk implements Stringable
{
    protected TagGenerator $tagGenerator;

    public function __construct(
        public Manifest $manifest,
        public string $file,
        public string|null $src,
        public bool $isEntry,
        public bool $isDynamicEntry,
        public bool $isLegacyEntry,
        public Collection $css,
        public Collection $imports,
        public Collection $dynamicImports,
        public Collection $assets,
        public string|null $integrity = null
    ) {
        $this->tagGenerator = app(TagGenerator::class);
    }

    /**
     * Generates a manifest entry from an array.
     */
    public static function fromArray(Manifest $manifest, array $manifestEntry): static
    {
        $file = $manifestEntry['file'] ?? '';
        $isEntry = $manifestEntry['isEntry'] ?? false;
        $isLegacyEntry = str_contains($file, '-legacy');

        return new static(
            manifest: $manifest,
            file: $file,
            src: $manifestEntry['src'] ?? null,
            isEntry: $isEntry,
            isDynamicEntry: $manifestEntry['isDynamicEntry'] ?? false,
            isLegacyEntry: $isLegacyEntry,
            css: collect($manifestEntry['css'] ?? []),
            imports: collect($manifestEntry['imports'] ?? []),
            dynamicImports: collect($manifestEntry['dynamicImports'] ?? []),
            assets: collect($manifestEntry['assets'] ?? []),
            integrity: $manifestEntry['integrity'] ?? null
        );
    }

    /**
     * Gets the tag for this entry.
     */
    public function getTag(): string
    {
        // If the file is a CSS file, the main tag is a style tag.
        if (Str::endsWith($this->file, '.css')) {
            return $this->tagGenerator->makeStyleTag($this->getAssetUrl(), $this);
        }

        // If the file is an entrypoint, we make a script tag.
        if ($this->isEntry) {
            return $this->tagGenerator->makeScriptTag($this->getAssetUrl(), $this);
        }
        
        // Otherwise, it's a preload tag.
        return $this->tagGenerator->makePreloadTag($this->getAssetUrl(), $this);
    }

    /**
     * Gets the style tags for this entry.
     */
    public function getStyleTags(): Collection
    {
        return $this->css->map(fn (string $path) => $this->tagGenerator->makeStyleTag($this->getPathAssetUrl($path)));
    }

    /**
     * Gets the tags for this chunk's imports.
     */
    public function getImportedTags(): Collection
    {
        return $this->imports->flatMap(fn (string $path) => Chunk::fromArray(
            $this->manifest,
            $this->manifest->getChunks()->get($path)
        )->getTags());
    }

    /**
     * Gets every script and style tag.
     */
    public function getTags(): Collection
    {
        return collect()
            ->push($this->getTag())
            ->push(...$this->getStyleTags())
            ->push(...$this->getImportedTags())
            ->unique();
    }

    /**
     * Gets the URL for this asset.
     */
    public function getAssetUrl(): string
    {
        return $this->getPathAssetUrl($this->file);
    }

    /**
     * Gets the complete path for the given asset path.
     */
    protected function getPathAssetUrl(string $path): string
    {
        // Determines the base path from the manifest path
        $public = str_replace('\\', '/', public_path());
        $base = str_replace($public, '', $this->manifest->getPath());
        $base = \dirname($base);
        $base = Str::of($base)
            ->replace('\\', '/')
            ->finish('/');

        return asset(sprintf('%s%s', $base, $path));
    }

    public function __toString(): string
    {
        return $this->getTags()->join("\n");
    }
}
