<?php

namespace Innocenzi\Vite\TagGenerators;

use Innocenzi\Vite\Chunk;
use Innocenzi\Vite\Vite;

final class CallbackTagGenerator implements TagGenerator
{
    public function __construct(protected DefaultTagGenerator $tagGenerator)
    {
    }

    public function makeScriptTag(string $url, Chunk $chunk = null): string
    {
        if (\is_callable(Vite::$makeScriptTagsCallback)) {
            return \call_user_func(Vite::$makeScriptTagsCallback, $url, $chunk);
        }

        return $this->tagGenerator->makeScriptTag($url, $chunk);
    }

    public function makeStyleTag(string $url, Chunk $chunk = null): string
    {
        if (\is_callable(Vite::$makeStyleTagsCallback)) {
            return \call_user_func(Vite::$makeStyleTagsCallback, $url, $chunk);
        }

        return $this->tagGenerator->makeStyleTag($url, $chunk);
    }

    public function makePreloadTag(string $url, Chunk $chunk = null): string
    {
        if (\is_callable(Vite::$makePreloadTagsCallback)) {
            return \call_user_func(Vite::$makePreloadTagsCallback, $url, $chunk);
        }

        return $this->tagGenerator->makePreloadTag($url, $chunk);
    }
}
