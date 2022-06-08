<?php

namespace Innocenzi\Vite\TagGenerators;

use Innocenzi\Vite\Chunk;

final class DefaultTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url, Chunk $chunk = null): string
    {
        if ($chunk?->isLegacyEntry) {
            return $this->makeLegacyScriptTag($url, $chunk);
        }

        return sprintf('<script type="module" src="%s"%s></script>', $url, $this->processIntegrity($chunk));
    }

    public function makeStyleTag(string $url, Chunk $chunk = null): string
    {
        return sprintf('<link rel="stylesheet" href="%s"%s />', $url, $this->processIntegrity($chunk));
    }

    public function makePreloadTag(string $url, Chunk $chunk = null): string
    {
        return sprintf('<link rel="modulepreload" href="%s"%s />', $url, $this->processIntegrity($chunk));
    }

    protected function makeLegacyScriptTag(string $url, Chunk $chunk = null): string
    {
        if (str_contains($chunk?->src, 'vite/legacy-polyfills')) {
            $safariFix = '<script nomodule>!function(){var e=document,t=e.createElement("script");if(!("noModule"in t)&&"onbeforeload"in t){var n=!1;e.addEventListener("beforeload",(function(e){if(e.target===t)n=!0;else if(!e.target.hasAttribute("nomodule")||!n)return;e.preventDefault()}),!0),t.type="module",t.src=".",e.head.appendChild(t),t.remove()}}();</script>';
            $bundleLoader = '<script type="module">!function(){try{new Function("m","return import(m)")}catch(o){console.warn("vite: loading legacy build because dynamic import is unsupported, syntax error above should be ignored");var e=document.getElementById("vite-legacy-polyfill"),n=document.createElement("script");n.src=e.src,n.onload=function(){var entries=Array.prototype.slice.call(document.querySelectorAll("[data-vite-legacy]"),0);entries.forEach(function(entry){System.import(entry.getAttribute("data-src")).catch(console.error)})},document.body.appendChild(n)}}();</script>';
            $legacyBundle = sprintf('<script nomodule id="vite-legacy-polyfill" src="%s"></script>', $url);

            return implode("\r\n", [$safariFix, $bundleLoader, $legacyBundle]);
        }

        return sprintf('<script nomodule src="%s"%s></script>', $url, $this->processIntegrity($chunk));
    }

    protected function processIntegrity(Chunk $chunk = null): string
    {
        if (!$chunk?->integrity) {
            return '';
        }

        $attributes = [
            'integrity' => $chunk->integrity,
            'crossorigin' => 'anonymous',
        ];

        return ' ' . collect($attributes)
            ->map(fn ($value, $key) => sprintf('%s="%s"', $key, $value))
            ->join(' ');
    }
}
