<?php

namespace Innocenzi\Vite\TagGenerators;

use Innocenzi\Vite\Vite;

final class CallbackTagGenerator implements TagGenerator
{
    public function __construct(protected DefaultTagGenerator $tagGenerator)
    {
    }

    public function makeScriptTag(string $url): string
    {
        if (\is_callable(Vite::$makeScriptTagsCallback ?? null)) {
            return \call_user_func(Vite::$makeScriptTagsCallback, $url);
        }

        return $this->tagGenerator->makeScriptTag($url);
    }

    public function makeStyleTag(string $url): string
    {
        if (\is_callable(Vite::$makeStyleTagsCallback ?? null)) {
            return \call_user_func(Vite::$makeStyleTagsCallback, $url);
        }

        return $this->tagGenerator->makeStyleTag($url);
    }
}
