<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Stringable;

class ManifestEntry implements Htmlable, Stringable
{
    public string $file;
    public string $src;
    public bool $isEntry;
    public bool $isDynamicEntry;
    public Collection $css;
    public Collection $dynamicImports;

    /**
     * Generates a manifest entry from an array.
     */
    public static function fromArray(array $manifestEntry): ManifestEntry
    {
        $entry = new ManifestEntry();
        $entry->src = $manifestEntry['src'] ?? '';
        $entry->file = $manifestEntry['file'] ?? '';
        $entry->isEntry = $manifestEntry['isEntry'] ?? false;
        $entry->isDynamicEntry = $manifestEntry['isDynamicEntry'] ?? false;
        $entry->dynamicImports = Collection::make($manifestEntry['dynamicImports'] ?? []);
        $entry->css = Collection::make($manifestEntry['css'] ?? []);

        return $entry;
    }

    /**
     * Gets the script tag for this entry.
     */
    public function getScriptTag(): string
    {
        return sprintf('<script type="module" src="%s"></script>', $this->asset($this->file));
    }

    /**
     * Gets the style tags for this entry.
     */
    public function getStyleTags(): Collection
    {
        return $this->css->map(fn (string $path) => sprintf('<link rel="stylesheet" href="%s" />', $this->asset($path)));
    }

    /**
     * Gets every appliacable tag.
     */
    public function getTags(): Collection
    {
        return Collection::make()
            ->push($this->getScriptTag())
            ->merge($this->getStyleTags());
    }

    /**
     * Gets the complete path for the given asset path.
     */
    protected function asset(string $path): string
    {
        return asset(sprintf('/%s/%s', config('vite.build_path'), $path));
    }

    /**
     * Gets the resources for this entry.
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->getTags()->join('');
    }

    public function __toString()
    {
        return $this->toHtml();
    }
}
