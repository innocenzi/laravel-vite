---
title: Inertia
outline: deep
---

# Inertia

## Overview

Inertia and Vite are a great match. Both provide an excellent developer experience and a very productive set of tools to work on a full-stack, modern Laravel monolith.

This documentation provides information about setting up Vite and Inertia together, which, rest assured, is a trivial process.

## Initial setup

### Using the preset

The simplest way of scaffolding a new Laravel application using Inertia and Vite is to apply the [distributed preset](https://github.com/laravel-presets/inertia) on a fresh Laravel installation. 

```sh
> composer create-project laravel/laravel vite-inertia
> cd vite-inertia
> npx @preset/cli apply laravel:inertia
```

This preset is a script that adds and configures Vite, Inertia, Tailwind CSS and Pest. Once this command has finished, everything is ready and you can start developing.

### Conventions

If you are used to the conventions from the Inertia ecosystem, you may notice some changes:
- The main entrypoint is `resources/scripts/main.ts` instead of `resources/js/app.js`
- Pages are stored in `resources/views/pages` instead of `resources/js/Pages`
- Components are stored in `resources/views/components` instead of `resources/js/Shared`
- Components are stored in `resources/views/layouts` instead of `resources/js/Layouts`
- File and directory names use `kebab-case` instead of `StudlyCase`
- Inertia pages components can be referenced using dots instead of just slashes

We strongly recommend following these conventions, as it creates the foundations for a consistent and organized architecture.

:::tip Don't like these defaults?
You can easily create your own Inertia preset by extending `laravel-presets/inertia`. Learn more about [extending presets](https://preset.dev/actions/apply-nested-preset) in [its documentation](https://preset.dev).

Alternatively, you can use the `--no-tailwindcss` or `--no-pest` flags to skip their installation.
:::

### From scratch

If you don't want to use the preset, for instance if you are using [Laravel Breeze](https://github.com/laravel/breeze) or [Jetstream](https://github.com/laravel/jetstream), we recommand following the [official Inertia documentation](https://inertiajs.com/) and then switching from Webpack to Vite using our [quick start guide](/guide/quick-start#in-an-existing-project). It's not much, but it's honest work.

## Serving pages

### Overview

Inertia requires you to provide a `resolve` function that takes a page name as a parameter and returns a page component.

With Webpack, the standard way of doing that is by using the `require` statement, which bundles every specified component thanks to globs. 

With Vite though, you must use [`import.meta.glob` or `import.meta.globEager`](https://vitejs.dev/guide/features.html#glob-import) to instruct Vite which files to bundle.

### Example implementation

If you used the [distributed preset](/guide/extra-topics/inertia#initial-setup), this is already done for you. If not, here is an example:

```ts
/**
 * Imports the given page component from the page record.
 */
function resolvePageComponent(name: string, pages: Record<string, any>) {
  for (const path in pages) {
    if (path.endsWith(`${name.replace('.', '/')}.vue`)) {
      return typeof pages[path] === 'function'
        ? pages[path]()
        : pages[path]
    }
  }

  throw new Error(`Page not found: ${name}`)
}

// Creates the Inertia app, nothing fancy.
createInertiaApp({
  resolve: (name) => resolvePageComponent(name, import.meta.glob('../views/pages/**/*.vue')),
  setup({ el, app, props, plugin }) {
    createApp({ render: () => h(app, props) })
      .use(plugin)
      .mount(el)
  },
})
```

## Server-side rendering

Inertia provides an implementation for server-side rendering, which you can learn about in [their documentation](https://inertiajs.com/server-side-rendering).

:::tip Using a single development server
The following guide is the simplest way to use server-side rendering with what's already been implemented. However, Vite supports running in middleware mode, so it's possible to create a single server instead of two.

Here is an [example implementation](https://gist.github.com/innocenzi/48d95f99acc70ce8f763112f23147bdb).
:::

### Creating the server

To use it with Vite, you need to create a new `ssr.ts` file with almost the same content as `main.ts`:

```ts {1,4,8,13}
import { createSSRApp, h } from 'vue'
import { renderToString } from '@vue/server-renderer'
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import createServer from '@inertiajs/server'

// ...

createServer((page) => createInertiaApp({
  page,
  render: renderToString,
  resolve: (name) => resolvePageComponent(name, import.meta.globEager('../views/pages/**/*.vue')),
  setup: ({ app, props, plugin: inertia }) => {
    return createSSRApp({ render: () => h(app, props) })
      .use(inertia)
  }
}))
```

### Configuring Vite

The next thing to do is to add this file to the [`entrypoints.ssr`](/configuration/laravel-package#ssr) property of the `config/vite.php` configuration file.

```php
'entrypoints' => [
    'ssr' => 'resources/scripts/ssr.ts',
    'paths' => 'resources/scripts/main.ts',
]
```

### Adding `package.json` scripts

Finally, you may add the relevant scripts to your `package.json`.

```json
{
  "scripts": {
    "dev": "vite",
    "dev:ssr": "node public/build/main/ssr.js",
    "build": "vite build",
    "build:ssr": "vite build --ssr"
  }
}
```

You may now run `npm run build:ssr && npm run dev:ssr` in a separate terminal to start your development server.
