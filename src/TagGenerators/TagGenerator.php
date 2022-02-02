<?php

namespace Innocenzi\Vite\TagGenerators;

interface TagGenerator
{
    public function makeScriptTag(string $url, array $attributes = []): string;

    public function makeStyleTag(string $url, array $attributes = []): string;
}
