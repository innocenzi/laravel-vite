---
title: Development
editLink: true
---

# Development

## Development server

Make sure your dependencies are installed, and run the `dev` script. It will start Vite's development server.

```bash
yarn # npm install
yarn dev # npm run dev
```

[Hot Module Replacement](https://vitejs.dev/guide/features.html#hot-module-replacement) is enabled: editing a Blade file will trigger a full-page refresh, but editing assets which Vite understand, such as Vue single-file components or JavaSript files, will trigger partial reloads.

## Automatic entrypoints

By default, scripts in `resources/scripts` are considered entrypoints, and will be included in Vite's configuration. To include them in your Blade files, you can use the [`@vite` directive](#directives).

You can update the entrypoint directories in the [configuration](/guide/configuration#entrypoints).

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
	<!-- Will generate:
	<script type="module" src="http://localhost:3000/@vite/client"></script>
	<script type="module" src="http://localhost:3000/resources/scripts/app.ts"></script>
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
	<!-- Will generate:
	<script type="module" src="http://localhost:3000/@vite/client"></script>
	<script type="module" src="http://localhost:3000/resources/scripts/main.ts"></script>
	<script type="module" src="http://localhost:3000/resources/js/some-script.js"></script>
	-->
</head>
```

Note that in order to enable Hot Module Replacement, you need to include the client script manually with the `@client` directive.

### `@client`

This directive includes the client script. It is not needed if you used `@vite` without parameters, but it is needed otherwise.

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
