---
title: Multiple configurations
---

# Multiple configurations

Vite currently does not support writing multiple bundles. It will always write one manifest, with potentially a `vendor.js` file containing third-party and shared code.

Sometimes though, you may need to create multiple bundles, and in that case you will need multiple configuration files.

## Creating multiple configurations

Say you need a specific bundle for a back-office that uses different dependencies than the front-office. This documentation will guide you through that setup.

You will need to update `config/vite.php`, create a new `vite.back-office.config.ts`, give the configuration name to the `@vite` directive and run slightly different development and build commands.

## Updating Laravel Vite's config

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
        │   ├─ back-office.a2c636dd.js
        │   └─ back-office.65bd481b.css
        └─ manifest.json
```

## Creating Vite's configuration file

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

## Using config-specific commands

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
