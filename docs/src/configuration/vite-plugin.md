---
title: Vite plugin configuration
outline: deep
---

# Vite plugin

This section explains each configuration property available for the Vite plugin.

:::info As a reminder, these options can be used when calling the default export of <code>vite-plugin-laravel</code>:

```ts
import { defineConfig } from 'vite'
import laravel from 'vite-plugin-laravel'

export default defineConfig({
	plugins: [
		laravel({ /* ... */ })
	]
})
```
:::

## `config`

This option can be one of the following: 
- A path to a file that contains a JSON version of the Laravel package configuration.
- An object representing the Laravel package configuration.
- The name of a configuration defined in `config/vite.php`'s `configs`.
- `false` to disable reading the configuration from the path defined in the `CONFIG_PATH_VITE` environment variable.

## `php`

The path to the PHP executable. By default, the path in the the `PHP_EXECUTABLE_PATH` environment variable will be used, or `php` if it's not defined.

## `postcss`

This option is a shortcut for Vite's `css.postcss.plugins` option. You can read more about that in [their documentation](https://vitejs.dev/config/#css-postcss).

## `ssr`

This option has the shape of [Vite's `ssr` option](https://vitejs.dev/config/#optimizedeps-esbuildoptions) and is conditionally injected into the configuration when using building for SSR, that is when using the `--ssr` flag.

## `updateTsConfig`

This option defines whether to update the `tsconfig.json` file with the aliases defined in `config/vite.php`'s `aliases`.

## `allowOverrides`

This option defines whether overrides defined by the user should be taken into account. This is `true` by default.

For instance, the `--host 0.0.0.0` flag won't work if you set `allowOverrides` to `false`.
