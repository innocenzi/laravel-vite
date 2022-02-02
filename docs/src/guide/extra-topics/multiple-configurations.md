---
title: Multiple configurations
outline: deep
---

# Multiple configurations

Currently, Vite does not have support for generating multiple bundles. It will always write one manifest, with potentially a `vendor.js` file containing third-party and shared code.

Sometimes though, you may need to have separated bundles for different parts of your application, and in that case you will need multiple configuration files.

## Overview

Say you need a separate bundle for a back-office that uses a different stack than the front-office.

You will need to add a configuration to `config/vite.php`, create a new `vite.back-office.config.ts` file, pass the configuration name to the `@vite` directive and run slightly different development and build commands.

## Configuring the Laravel package

Open `config/vite.php` and duplicate the `default` array located in `configs`, and call it `back-office`:

```php
return [
	// ...
	'configs' => [
		'default' => [
			// ...
		],
		
		// Your new back-office configuration
		'back-office' => [
			'entrypoints' => [
				'paths' => 'resources/scripts/back-office.ts',
				// ...
			],
			'dev_server' => [
				'url' => env('DEV_SERVER_URL', 'http://localhost:3001'),
				// ...
			],
			'build_path' => 'build/back-office',
		],
	],
];
```

The important parts are the name of the config (the key of the array, `back-office`), `entrypoints`, `dev_server.url` and `build_path`. If you work on multiple configurations at the same time and need their development server started concurrently, their URL must be different. In the above exemple, note that the port was changed from `:3000` to `:3001`.

Aditionally, the `build_path` must differ as to not override other configurations' builds. In this case, the following will be generated:

```md
public/
└─ build/
	└─ back-office/
		├─ assets/
		│	├─ back-office.a2c636dd.js
		│	└─ back-office.65bd481b.css
		└─ manifest.json
```

## Configuring Vite

Then, create a Vite configuration file: `vite.back-office.config.ts`. The name there is important, as it's used to infer the configuration name. 

Optionally, if you need to name the configuration file something else, you may use the `config` option of the plugin:

```ts
import laravel from 'vite-plugin-laravel'
import { defineConfig } from 'vite'

export default defineConfig({
	plugins: [
		laravel({ config: 'back-office' })
	]
})
```

## Chosing which config to use

Instead of using `vite dev` or `vite build`, which will use the `default` configuration, you will need to specify the Vite configuration file you want to use.

```sh
> npx vite --config vite.back-office.config.ts
```

It is easier to add commands to `package.json` instead of typing the entire configuration name each time:

```json
{
	"scripts": {
		"dev": "vite",
		"dev:back": "vite --config vite.back-office.config.ts",
		"build": "vite build",
		"build:back": "vite build --config vite.back-office.config.ts",
		"build:all": "npm run build && npm run build:test"
	},
}
```

With the above, you can just run `npm run dev:back` or `npm run build:back`.

## Using the directives

Finally, to include assets from a specific configuration, simply pass its name to the `@vite` directive:

```php
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- -->
		@vite('back-office')
	</head>
	<body>
		<!-- -->
	</body>
</html>
```
