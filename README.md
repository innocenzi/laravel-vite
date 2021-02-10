<p align="center">
  <br />
  <a href="https://github.com/innocenzi/laravel-vite">
    <img width="100" src="./.github/assets/logo.svg" alt="Logo of Vite">
  </a>
  <br />
</p>

<h2 align="center">Laravel Vite</h2>

<p align="center">
  <a href="https://vitejs.dev">Vite</a> integration for the Laravel framework.
  <br />
  <br />
  <a href="https://github.com/innocenzi/laravel-vite/actions?query=workflow%3Atests">
    <img alt="Status" src="https://github.com/innocenzi/laravel-vite/workflows/tests/badge.svg">
  </a>
  <span>&nbsp;</span>
  <a href="https://packagist.org/packages/innocenzi/laravel-vite">
    <img alt="packagist" src="https://img.shields.io/packagist/v/innocenzi/laravel-vite">
  </a>
  <span>&nbsp;</span>
  <a href="https://www.npmjs.com/package/laravel-vite">
    <img alt="npm" src="https://img.shields.io/npm/v/laravel-vite">
  </a>
  <br />
  <br />
  <pre align="center">composer require innocenzi/laravel-vite</pre>
</p>
<br />

## Installation

### On a fresh project

> **Narrator**: the preset is not actually available yet. Soon™.

~~A [preset](https://usepreset.dev) is available to perform the installation. It will remove Laravel Mix, install this package and scaffold Vite into your Laravel application.~~

```bash
$ npx apply innocenzi/laravel-vite

# On Mac
$ npx apply innocenzi/laravel-vite --ignore-existing
```

### Manually

You'll need to install this package, install Vite, create its configuration file, and potentially adapt your application to work with Vite.

**1.** Require the package with composer.

```bash
$ composer require innocenzi/laravel-vite
```

**2.** (Optional) Add the `laravel-vite` NPM package.

```bash
$ yarn add laravel-vite --dev
```

The purpose of this package is to slightly simplify the Vite configuration file for its usage with Laravel.

**3.** Create a `vite.config.ts` file at the root of your project.

```ts
import { createViteConfiguration } from "laravel-vite";

export default createViteConfiguration()
    .withEntry("resources/js/app.js");
```

If you don't want to use `laravel-vite`, your configuration can look like that:

```ts
import { defineConfig } from "vite";

export default defineConfig({
    publicDir: "resources/static",
    build: {
        manifest: true,
        outDir: "public/build",
        rollupOptions: {
            input: ["resources/js/app.js"],
        },
    },
});
```

**4.** Use the `@vite` directive insided your Blade files.


You may use the `@vite` directive in pages in which you want to include your scripts or styles bundled with Vite. 

```blade
<head>
  <!-- -->
  @vite('client')
  @vite('resources/scripts/main.ts')
</head>
```

## Usage

### The Blade directive

Used without parameters, `@vite` will include the Vite client and every entry point defined in your configuration. 

```blade
<!-- Will include everything -->
@vite
```

If a parameter is given, the directive will include the scripts and styles related to that entry point.

```blade
<!-- Will include the main.ts script -->
@vite('resources/scripts/main.ts')
```

This example is what is needed when you need a granular approach, like when you have specific scripts for specific files. But in this case, the Vite client is not automatically included, so you need to add it manually:

```blade
<!-- Will include the Vite client and the main.ts script -->
@vite('client')
@vite('resources/scripts/main.ts')
```

In production, the client script is ignored.


### `laravel-vite` helper

The `laravel-vite` helper is an NPM package that provides a very simple API, making the Vite configuration file cleaner. You may add it to your development dependencies along with Vite:

```bash
$ yarn add laravel-vite --dev
# or
$ npm i laravel-vite -D
```

It exports a `createViteConfiguration` function that you can export from your `vite.config.ts` file. By default, it will assume you store your static assets in `resources/static` and you want your build to be generated in `public/build`.

An entry point can be added with the `withEntry` method:

```ts
// vite.config.ts
import { createViteConfiguration } from "laravel-vite";

export default createViteConfiguration()
  .withEntry("resources/js/app.js")
```

To change the static assets directory or the build path, use `withStaticAssets` and `withOutput`, respectively: 

```ts
// vite.config.ts
import { createViteConfiguration } from "laravel-vite";

export default createViteConfiguration()
  .withEntry("resources/js/app.js")
  .withStaticAssets("resources/public")
  .withOutput("public/dist")
```

In most cases, you don't need to update your Vite configuration, except for adding entry points. When you do change it, make sure you update the `config/vite.php` configuration file accordingly.

## Package configuration

If you changed Vite's build path, its assets directory or the HMR settings, you will need to update the configuration accordingly. By default, the configuration is compatible with the `laravel-vite` helper.

```php
// config/vite.php 
<?php

return [
    'build_path' => \public_path('build'),
    'assets_path' => 'build',
    'hmr_url' => 'http://localhost:3000',
];
```

### `build_path`

This is used to determine where the `manifest.json` file is located. As I am writing this documentation, I realize that this should only be needed in production, but the way this package works require the `manifest.json` file to exist in development mode as well. Will fix soon. :D

### `assets_path`

This is used to determine the path of an asset when generating a script or style tag from the Vite manifest.

### `hmr_url`

This is used to generate the script tags for the client and for other assets when in development mode.

<br />
<p align="center">
  <br />
  <br />
  ·
  <br />
  <br />
  <sub>Made with ❤︎ by <a href="https://github.com/enzoinnocenzi">Enzo Innocenzi</a>. <br />
  Contributions are welcome.
</p>
