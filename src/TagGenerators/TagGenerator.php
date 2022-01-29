<?php

namespace Innocenzi\Vite\TagGenerators;

interface TagGenerator
{
    public function makeScriptTag(string $url): string;

    public function makeStyleTag(string $url): string;
}
