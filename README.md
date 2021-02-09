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
<br />

## Installation

Require the package with composer:

```bash
$ composer require innocenzi/laravel-vite
```

Install the optional `laravel-vite` NPM package:

```bash
$ yarn add innocenzi/laravel-vite --dev
```

Create a `vite.config.ts` file at the root of your project:

<!-- prettier-ignore -->
```ts
import { createViteConfiguration } from "laravel-vite";

export default createViteConfiguration()
    .withEntry("resources/js/app.js");
```

Or without `laravel-vite`:

```ts
import { defineConfig } from "vite";

export default defineConfig({
    publicDir: "resources/static",
    build: {
        manifest: true,
        rollupOptions: {
            input: ["resources/js/app.js"],
        },
        outDir: "public/build",
    },
});
```

## Usage

In your Blade template, use the `@vite` directive. By default, it will include every single entry point you declared. If you want a more granular approach, you need to specifically include the entry points you need:

```blade
@vite('client')
@vite('resources/scripts/main.ts')
```

Note that `@vite('client')` is required if you don't use `@vite` without parameters.

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
