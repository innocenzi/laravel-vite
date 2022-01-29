<?php

namespace Innocenzi\Vite\TagGenerators;

final class DefaultTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url): string
    {
        return sprintf('<script type="module" src="%s"></script>', $url);
    }

    public function makeStyleTag(string $url): string
    {
        return sprintf('<link rel="stylesheet" href="%s" />', $url);
    }
}
