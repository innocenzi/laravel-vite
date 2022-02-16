---
title: Upgrade from 0.1.x
outline: deep
---

# Upgrade from 0.1.x

## Overview

There were many changes since 0.1.x, but catching up should take no longer than 15 minutes. 

The main changes are related to the configuration files, which structure changed drastically in order to support new features.

## High-impact changes

### Update `innocenzi/laravel-vite`

Although no major breaking change is expected, the package is currently in beta, so you need to specify the version:

```sh
> composer require innocenzi/laravel-vite:0.2.*
```

### Replace `laravel-vite` by `vite-plugin-laravel`

First, make the changes to your dependencies:

```sh
npm remove laravel-vite
npm i -D vite-plugin-laravel
```

The `defineConfig` was previously a wrapper around the Vite configuration, but it introduced a few edge-cases and implied some workarounds internally to work properly. 

We decided to go the normal way by providing a normal Vite plugin instead.

Open `vite.config.ts` and make the following changes: 

- Use `vite`'s `defineConfig` instead of `laravel-vite`'s, and use the `plugin` option instead of chaining `withPlugin` to `defineConfig`:

```diff {3,11}
- import { defineConfig } from 'laravel-vite'
+ import { defineConfig } from 'vite'
+ import laravel from 'vite-plugin-laravel'
  import vue from '@vitejs/plugin-vue'

- export default defineConfig()
- 	.withPlugin(vue)
+ export default defineConfig({
+   plugins: [
+     vue()
+     laravel()
+   ]
+ })
```

- Replace `withPostCss` with the `postcss` option of the plugin:

```diff {10,11,12,13}
- export default defineConfig()
- 	.withPostCSS([
- 		tailwindcss(),
- 		autoprefixer(),
- 	])
+ export default defineConfig({
+   plugins: [
+     vue()
+     laravel({
+       postcss: [
+      		tailwindcss(),
+      		autoprefixer(),
+       ]
+     })
+   ]
+ })
```

Note that `withCertificates` has no equivalent since it is no longer needed. Instead, follow the documentation on [using `https` locally](/guide/essentials/development.html#using-http-over-tsl).

### Update `config/vite.php`

#### Configuration format

The configuration format changed and a few options moved. We recommend grabbing the new configuration file [from the source](https://github.com/innocenzi/laravel-vite/blob/main/config/vite.php) and replacing your current one.

Then, you can configure it again, with the following changes:
  
- `entrypoints` -> `configs.default.entrypoints.path`
- `ignore_patterns` -> `configs.default.entrypoints.ignore`
- `dev_url` -> `configs.default.dev_server.url`
- `ping_timeout` -> `configs.default.dev_server.ping_timeout`
- `ping_url` -> `configs.default.dev_server.ping_url`
- `build_path` -> `configs.default.build_path`
- `commands` -> `commands.artisan`

### The `@vite` directive

This directive's first parameter was previously used to specify an entrypoint to include. If you were using that functionality, you should now use the `@tag` directive. 

You can learn more about the new directives in [their documentation](/guide/features/directives.html#tag).

## Low-impact changes

### The `vite:aliases` artisan command

This command has been renamed to `vite:tsconfig`. Additionally, it will now preserve the indentation of the existing `tsconfig.json` if there is one.

### The `Vite::generateTagsUsing` method 

This method was used to change how the tags were generated. 

It no longer exists, `Vite::makeScriptTagsUsing` and `Vite::makeStyleTagsUsing` should be used instead. You can learn more about that in the [tag generation](/guide/essentials/tag-generation.html) documentation.

### The `Vite::withoutManifest` method

This method was used to deny Vite from trying to use the manifest. It was specifically useful in tests. 

It no longer exists, and the `vite.testing_use.manifest` configuration option should be used instead. 

In addition to that, you can read the [server and manifest modes](/guide/essentials/server-and-manifest-modes.html) documentation to learn how to control which of the manifest or development server should be used.
