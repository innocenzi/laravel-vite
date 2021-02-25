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

[Hot Module Replacement](https://vitejs.dev/guide/features.html#hot-module-replacement) is enabled: editing a Blade file will trigger a full-page refresh, but editing assets which Vite understand, such as Vue single-file components or JavaSript files, will trigger partial reloads.

If you are working locally and the development server is not working, Laravel Vite will try to read the manifest instead.

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

In order to support Visual Studio Code, the development server will generate a `tsconfig.json` file (or update the existing one) with the configured aliases. Updating them will required a proper server restart.

Additionally, you can manually generate or update this file with the `vite:aliases` Artisan command.

```bash
php artisan vite:aliases
```

## Assets

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

:::warning
This works well when using Blade, but **there is currently an unsolved issue when referencing assets in files processed by Vite**, such as a Vue or CSS file. In development, URLs will not be properly rewritten.

That issue is tracked here: https://github.com/vitejs/vite/issues/2196.
:::

If you want to use a directory other than `resources/static`, you can change the [`public_directory` option](/guide/configuration#public-directory).
