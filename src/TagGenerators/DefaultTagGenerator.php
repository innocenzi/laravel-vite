<?php

namespace Innocenzi\Vite\TagGenerators;

use Innocenzi\Vite\Chunk;

final class DefaultTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url, Chunk $chunk = null): string
    {
        return sprintf('<script type="module" src="%s"%s></script>', $url, $this->processIntegrity($chunk));
    }

    public function makeStyleTag(string $url, Chunk $chunk = null): string
    {
        return sprintf('<link rel="stylesheet" href="%s"%s />', $url, $this->processIntegrity($chunk));
    }
    
    protected function processIntegrity(Chunk $chunk = null): string
    {
        if (! $chunk?->integrity) {
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
