<?php

namespace Innocenzi\Vite\TagGenerators;

final class DefaultTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url, array $attributes = []): string
    {
        return sprintf('<script type="module" src="%s"%s></script>', $url, $this->processAttributes($attributes));
    }

    public function makeStyleTag(string $url, array $attributes = []): string
    {
        return sprintf('<link rel="stylesheet" href="%s"%s />', $url, $this->processAttributes($attributes));
    }
    
    protected function processAttributes(array $attributes = []): string
    {
        $attributes = collect($attributes)->map(function ($value, $key) {
            if ($value === null) {
                return null;
            }

            if (\is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            if ($value === '') {
                return $key;
            }

            return sprintf('%s="%s"', $key, (string) $value);
        })->filter()->join(' ');

        if (\strlen($attributes) > 0) {
            $attributes = ' ' . $attributes;
        }

        return $attributes;
    }
}
