<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stringable;

class ManifestEntry implements Htmlable, Stringable
{
    public string $file;
    public string $src;
    public bool $isEntry;
    public bool $isDynamicEntry;
    public Collection $css;
    public Collection $dynamicImports;

    const SCRIPT_TAG = 'script';
    const STYLE_TAG = 'style';

    /**
     * Generates a manifest entry from an array.
     */
    public static function fromArray(array $manifestEntry): ManifestEntry
    {
        $entry = new static();
        $entry->src = $manifestEntry['src'] ?? '';
        $entry->file = $manifestEntry['file'] ?? '';
        $entry->isEntry = $manifestEntry['isEntry'] ?? false;
        $entry->isDynamicEntry = $manifestEntry['isDynamicEntry'] ?? false;
        $entry->dynamicImports = Collection::make($manifestEntry['dynamicImports'] ?? []);
        $entry->css = Collection::make($manifestEntry['css'] ?? []);

        return $entry;
    }

    /**
     * Gets the tag for this entry.
     */
    public function getTag(): string
    {
        // If the file is a CSS file, the main tag is a style tag.
        if (Str::endsWith($this->file, '.css')) {
            return $this->makeStyleTag($this->asset($this->file));
        }

        // Otherwise, it's a script tag.
        return $this->makeScriptTag($this->asset($this->file));
    }

    /**
     * Gets the style tags for this entry.
     */
    public function getStyleTags(): Collection
    {
        return $this->css->map(fn (string $path) => $this->makeStyleTag($this->asset($path)));
    }

    /**
     * Gets every applicable tag.
     */
    public function getTags(): Collection
    {
        return Collection::make()
            ->push($this->getTag())
            ->merge($this->getStyleTags());
    }

    /**
     * Gets the complete path for the given asset path.
     */
    public function asset(string $path): string
    {
        return asset(sprintf('/%s/%s', config('vite.build_path'), $path));
    }

    /**
     * Generates a script tag.
     */
    public function makeScriptTag(string $url): string
    {
        if (\is_callable(Vite::$generateTagsUsing)) {
            return \call_user_func(Vite::$generateTagsUsing, $url, static::SCRIPT_TAG, $this);
        }

        return sprintf('<script type="module" src="%s"></script>', $url);
    }

    /**
     * Generates a style tag.
     */
    public function makeStyleTag(string $url): string
    {
        if (\is_callable(Vite::$generateTagsUsing)) {
            return \call_user_func(Vite::$generateTagsUsing, $url, static::STYLE_TAG, $this);
        }

        return sprintf('<link rel="stylesheet" href="%s" />', $url);
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
