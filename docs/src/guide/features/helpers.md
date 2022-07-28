---
title: Helpers
---

# Helpers

## Overview

Helpers are global functions you may use to help you get informations regarding manifests and assets. Generally speaking, it's best not to use them and just rely on the [directives](/guide/features/directives), but they are here if you need.

## `vite`

This function returns an instance of the default Vite configuration, or the specified one if any.

```php
echo vite()->getName(); // prints the value of config('vite.default')
```

## `vite_entry`

This function returns the URL for the given entrypoint. If the asset is not registered as an entrypoint, an exception will be thrown.
You can specify the configuration name as the second parameter.

```php
echo vite_entry('main');
// Local: https://localhost:5173/resources/scripts/main.ts
// Prod:  https://app-url.test/build/assets/test.a2c636dd.js
```

## `vite_asset`

This function returns a valid URL for the given asset path. The path is not verified against the configured entrypoints.

- In manifest mode, Laravel's `asset` is used and the `build_path` is prepended to the given path.
- In server mode, the development server URL is used and the path is appended as-is.

You can specify the configuration name as the second parameter.

```php
echo vite_asset('/file.txt');
// Local: https://localhost:5173/file.txt
// Prod:  https://app-url.test/build/file.txt
```

## `vite_tag`

This function returns the tag for the given entrypoint. It's the exact same as the [`@tag` directive](/guide/features/directives#tag). You can specify the configuration name as the second parameter.

```php
echo vite_tag('main');
// Local: <script type="module" src="http://localhost:5173/resources/scripts/main.ts"></script>
//
// Prod:  <script type="module" src="https://app-url.test/build/assets/main.a2c636dd.js"></script>
//        <link rel="stylesheet" href="https://app-url.test/build/assets/main.65bd481b.css" />
```

## `vite_tags`

This function returns the tags for the Vite client script and every configured entrypoint. It's the exact same as the [`@vite` directive](/guide/features/directives#tag). You can specify the configuration name as the first parameter.

```php
echo vite_tags();
// Local: <script type="module" src="http://localhost:5173/@vite/client"></script>
//        <script type="module" src="http://localhost:5173/resources/scripts/main.ts"></script>
//
// Prod:  <script type="module" src="https://app-url.test/build/assets/main.a2c636dd.js"></script>
//        <link rel="stylesheet" href="https://app-url.test/build/assets/main.65bd481b.css" />
```

## `vite_client`

This function returns the tag for the Vite client script. It's the exact same as the [`@client` directive](/guide/features/directives#client). You can specify the configuration name as the first parameter.

```php
echo vite_client();
// Local: <script type="module" src="http://localhost:5173/@vite/client"></script>
// Prod:  nothing
```

## `vite_react_refresh_runtime`

This function returns the tag for the Vite client script. It's the exact same as the [`@react` directive](/guide/features/directives#react). You can specify the configuration name as the first parameter.
