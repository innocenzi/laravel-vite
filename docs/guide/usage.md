---
title: Usage
editLink: true
---

# Usage

## Development

Make sure your dependencies are installed, and run the `dev` script. It will start Vite's development server.

```bash
yarn # npm install
yarn dev # npm run dev
```

[Hot Module Replacement](https://vitejs.dev/guide/features.html#hot-module-replacement) is enabled: editing a Blade file will trigger a full-page refresh, but editing assets which Vite understand, such as Vue single-file components or JavaScript files, will trigger partial reloads.

If you are working locally and the development server is not started, Laravel Vite will try to read the manifest instead. If your environment doesn't allow Laravel Vite to ping the development server, you can disable that feature by setting [`ping_timeout`](./configuration#ping-timeout) to `null`.

::: tip Development URL
Note that the development server only serves assets, **not your application**. To access your application, you have to use a server, like Laravel Valet or `php artisan serve`.
:::

## Automatic development server discovery

When developing locally, Laravel Vite will try to ping your development server in order to determine if it should use it or should use the manifest. This is handy when you want to visit your application without having the development server running because you already have a manifest generated.

While convenient, this feature can break in environments where the back-end can't directly ping the development server. In order to disable it, you can set `ping_timeout` in `config/vite.php` to `false`.

## Entrypoints

By default, scripts in `resources/scripts` are considered entrypoints, and will be included in Vite's configuration. To include them in your Blade files, you can use the [`@vite` directive](#directives).

You can update entrypoints in the [configuration](/guide/configuration#entrypoints).

## Directives

Laravel Vite includes a few directives that handles linking assets in development and in production with the same syntax.

### `@vite`

**Without arguments**, this directive will include all of the configured entrypoints. For instance, if you have a `resources/scripts/app.ts` file, using `@vite` in your Blade file will include it along with the development server's script.

<!-- prettier-ignore -->
```html
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title>Laravel</title>
	@vite
	<!--
	In development:
		<script type="module" src="http://localhost:3000/@vite/client"></script>
		<script type="module" src="http://localhost:3000/resources/scripts/app.ts"></script>
	In production:
		<script type="module" src="http://laravel.local/build/assets/app.66e83946.js"></script>
	-->
</head>
```

**With an argument**, this directive will simply link to the given script, without including the development server. If the script is located in the entrypoints directory, you can simply use the file name. If not, you need to use the full path, relative to the project's root.

<!-- prettier-ignore -->
```html
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title>Laravel</title>
	@client
	@vite('main')
	@vite('resources/js/some-script.js')
	<!-- 
	In development:
		<script type="module" src="http://localhost:3000/@vite/client"></script>
		<script type="module" src="http://localhost:3000/resources/scripts/main.ts"></script>
		<script type="module" src="http://localhost:3000/resources/js/some-script.js"></script>
	In production:
		<script type="module" src="http://laravel.local/build/assets/main.66e83946.js"></script>
		<script type="module" src="http://laravel.local/build/assets/some-script.6d3515d2.js"></script>
	-->
</head>
```

Note that in order to enable Hot Module Replacement, you need to include the client script manually with the `@client` directive.

### `@client`

This directive includes the client script. It is not needed if you used `@vite` without parameters, but it is needed otherwise.

### `@react`

This directive includes [React's refresh runtime](https://vitejs.dev/guide/backend-integration.html#backend-integration). It is not needed if you don't use React, and it must be added **before** `@vite` or `@client`.

## Helpers

In case you need to reference tags manually, a few global helpers are available.

| Directive                  | Equivalent helper              | Description                                                        |
| -------------------------- | ------------------------------ | ------------------------------------------------------------------ |
| `@vite` without parameters | `vite_tags()`                  | Generate tags for the Vite client and every configured entrypoint. |
| `@vite` with parameters    | `vite_entry(string $entry)`    | Generate tags that include the given entry.                        |
| `@client`                  | `vite_client()`                | Generate a script tag that includes the Vite client.               |
| `@react`                   | `vite_react_refresh_runtime()` | Generate a script tag that includes the React Refresh runtime.     |
| N/A                        | `vite_asset()`                 | Generate an [asset path](/guide/usage#static-assets).              |

## Import aliases

For convenience, the `@` alias is configured to point to the `resources` directory. This can be edited in the [configuration](/guide/configuration#aliases).

```ts
// resources/scripts/main.ts
import '@/css/app.css';

// resources/css/app.css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

In order to support Visual Studio Code, by default, the development server will generate a `tsconfig.json` file (or update the existing one) with the configured aliases. Updating them will require a proper server restart.

You can manually generate or update this file with the `vite:aliases` Artisan command.

```bash
php artisan vite:aliases
```

If you want to keep registering the aliases but not regenerate the `tsconfig.json` file, you can remove `vite:aliases` from the [`commands`](./configuration#commands) option.

## Static assets

Files stored in `resources/static` are served by Vite as if they were in the `public` directory. You can generate a path to an asset using `vite_asset()`. For instance, assuming you have a `cat.png` file in `resources/static/images`:

```html
<img src="{{ vite_asset('images/cat.png') }}" alt="A cute cat" />
<!-- 
In development:
	<img src="http://localhost:3000/images/cat.png" alt="A cute cat" />
In production:
	<img src="https://your-site.dev/build/images/cat.png " alt="A cute cat" />
-->
```

If you want to use a directory other than `resources/static`, you can change the [`public_directory` option](/guide/configuration#public-directory).

## Vite-processed assets

There is currently [an unsolved issue when referencing assets in files processed by Vite](https://github.com/vitejs/vite/issues/2196), such as a Vue or CSS file.

A way around this issue is to [automatically replace all asset URLs in the code](https://nystudio107.com/blog/using-vite-js-next-generation-frontend-tooling-with-craft-cms#vite-processed-assets) with a Vite plugin.

An other workaround would be to add a fallback route. This can be done by Laravel Vite by calling `Vite::redirectAssets()` in a service provider. Read more in the [troubleshooting](./troubleshooting) section.

Additionally, there is currently no way to get the path of a Vite-processed asset (eg. an image that was imported in a Vue SFC) from the back-end, since the manifest does not reference the original file path. In most cases, this should not be an issue, as this is not a common use case.

## PostCSS and Tailwind CSS

If a PostCSS configuration file is present, Vite understands it out of the box, so you don't need to take action. It is also possible to directly feed a list of plugin to Vite inside `vite.config.ts`: 

```ts
import { defineConfig } from 'laravel-vite'
import vue from '@vitejs/plugin-vue'
import tailwind from 'tailwindcss'
import autoprefixer from 'autoprefixer'

export default defineConfig()
	.withPlugin(vue)
	.withPostCSS([
		tailwind,
		autoprefixer
	])
```

If you are using Tailwind CSS and JIT, don't forget to setup `purge` to locate your template files: 

```js
// tailwind.config.js
module.exports = {
	mode: 'jit',
	purge: [
		'./resources/**/*.blade.php',
		'./resources/**/*.{js,ts,vue}',
	],
  // ...
```

Last, you may import Tailwind's CSS export instead of creating your own CSS file: 

```ts
// main.ts
import 'tailwindcss/tailwind.css'
```
