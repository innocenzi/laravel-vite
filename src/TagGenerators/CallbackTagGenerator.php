<?php

namespace Innocenzi\Vite\TagGenerators;

use Innocenzi\Vite\Vite;

final class CallbackTagGenerator implements TagGenerator
{
    public function __construct(protected DefaultTagGenerator $tagGenerator)
    {
    }

    public function makeScriptTag(string $url, array $attributes = []): string
    {
        if (\is_callable(Vite::$makeScriptTagsCallback)) {
            return \call_user_func(Vite::$makeScriptTagsCallback, $url, $attributes);
        }

        return $this->tagGenerator->makeScriptTag($url, $attributes);
    }

    public function makeStyleTag(string $url, array $attributes = []): string
    {
        if (\is_callable(Vite::$makeStyleTagsCallback)) {
            return \call_user_func(Vite::$makeStyleTagsCallback, $url, $attributes);
        }

        return $this->tagGenerator->makeStyleTag($url, $attributes);
    }
}
