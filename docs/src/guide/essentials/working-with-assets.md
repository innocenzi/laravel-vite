---
title: Working with assets
outline: deep
---

# Working with assets

## Overview

By default, Vite will detect and bundle assets so they can be versioned through the manifest file.

There are a few way to import assets, including referencing them in an `src` attribute or importing them via `import`. Both work with absolute, relative or aliased paths.

## References in code

Assets referenced in templates are statically detected by Vite. For instance, an image stored in `resources/images/logo.png` can be imported like the following:

```html
<img src="@/resources/images/logo.png" alt="My logo" />
```

Using `url()` in CSS files works the same way.

## Manual imports

Alternatively, assets can be imported with an `import` statement:

```js
import logo from '@/resources/images/logo.png'
document.getElementById('logo').src = logo
```

In this example, `@/resources/images/logo.png` will be resolved to `http://localhost:3000/images/logo.png` during development, and become `/assets/logo.2d8efhg.png` in the production build.

:::tip Import modifiers
Vite supports a few import modifiers, such as `?url` or `?raw`. You can learn more about this in [their documentation](https://vitejs.dev/guide/assets.html#the-public-directory).
:::


## Absolute URLs

When trying to reference assets stored in the `public` directory, you will need to tell Vite not to bundle them, otherwise the build will fail (since the content of `public` should not be processed through Vite).

One way of doing that is using a dynamic path or a variable. For instance, if you have an image stored in `public/logo.png`, you can reference it like the following:

```html
<img :src="'/logo.png'" alt="My logo" />
```

:::tip Avoid absolute URLs
If you can, it's better to bundle assets so they can be versioned and cache-busted. They can also be optimized by Vite and Rollup plugins.
:::
