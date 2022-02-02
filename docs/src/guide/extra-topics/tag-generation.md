---
title: Tag generation
---

# Tag generation

## Overview

By default, Laravel Vite generates basic script and style tags, with no specific attribute. However, you are free to override this behavior and use your own logic to generate the tags.

## Override via callbacks

You may call `Vite::makeScriptTagsUsing()` and `Vite::makeStyleTagsUsing()` in your application service provider or a middleware. 

```php
// Overrides script tag generation
Vite::makeScriptTagsUsing(function (string $url, Chunk $chunk = null): string {
    return sprintf('<script type="module" src="%s" defer></script>', $url);
});

// Overrides style tag generation
Vite::makeStyleTagsUsing(function (string $url, Chunk $chunk = null): string {
    return sprintf('<link rel="stylesheet" href="%s" crossorigin="anonymous" />', $url);
});
```

## Subresource Integrity

[Subresource Integrity](https://developer.mozilla.org/en-US/docs/Web/Security/Subresource_Integrity) is supported through [`vite-plugin-manifest-sri`](https://github.com/ElMassimo/vite-plugin-manifest-sri). When this plugin is registered, Vite will add `integrity` and `crossorigin` attributes on entrypoint tags. 

:::info Script-imported CSS files are not supported
The extended manifest file from [`vite-plugin-manifest-sri`](https://github.com/ElMassimo/vite-plugin-manifest-sri) does not add an `integrity` property to imported CSS. 

If you need to ensure CSS integrity, register them as [entrypoints](/configuration/laravel-package#entrypoints) instead.
:::

## Override the implementation

Alternatively, you may implement the `Innocenzi\Vite\TagGenerators\TagGenerator` interface. 

The [`interfaces.tag_generator` configuration option](/configuration/laravel-package#tag-generator) provides you with a convenient way of binding your custom implementation.
