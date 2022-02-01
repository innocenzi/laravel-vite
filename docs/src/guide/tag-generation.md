---
title: Tag generation
---

# Tag generation

## Overview

By default, Laravel Vite generates basic script and style tags, with no specific attribute. However, you are free to override this behavior and use your own logic to generate the tags.

## Override via callbacks

You may call `Vite::makeScriptTagsUsing()` and `Vite::makeStyleTagsUsing()` in your application provider or a middleware.

```php
// Overrides script tag generation
Vite::makeScriptTagsUsing(function (string $url): string {
    return sprintf('<script type="module" src="%s" defer></script>', $url);
});

// Overrides style tag generation
Vite::makeStyleTagsUsing(function (string $url): string {
    return sprintf('<link rel="stylesheet" href="%s" crossorigin />', $url);
});
```

## Override the implementation

Alternatively, you may implement the `Innocenzi\Vite\TagGenerators\TagGenerator` interface. 

The [`interfaces.tag_generator` configuration option](/configuration/laravel-package#tag-generator) provides you with a convenient way of binding your custom implementation.
