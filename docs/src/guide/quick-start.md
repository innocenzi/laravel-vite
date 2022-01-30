---
title: Quick start
---

# Installation

:::info Are you using <a href="https://inertiajs.com">Inertia</a>?
There is an Inertia preset for a fast and easy installation. You can check our [Inertia guide](/guide/extra-topics/inertia).
:::

## In a fresh project

The recommended and the easiest approach to get started with Laravel Vite is to use the preset. It's a single command that will:

- Delete `resources/js` and create `resources/scripts`
- Delete `webpack.mix.js` and remove the dependency to `laravel-mix`
- Add development dependencies on `vite` and `vite-plugin-laravel`
- Update `package.json`'s scripts to use Vite
- Add a dependency on `innocenzi/laravel-vite`
- Create a `vite.config.ts` configuration file
- Add a call to the `@vite` directive in `welcome.blade.php`

To apply the preset, run the following command:

```sh
> npx @preset/cli laravel:vite
```

Everything will be up and ready. You can open your `resources/scripts/main.ts` file and start from there.

## In an existing project

### Initial setup

Vite can replace Webpack in existing projects, but some changes will be necessary. Here are some common gotchas:

- Vite explicitly requires that all Vue single-file components imports include the `.vue` in the path.
- Vite is ESM-based and doesn't support `require` statements. Instead, you need to use `import`.
- Webpack

With that in mind, you can start replacing Mix. First, you can delete your `webpack.mix.js` and remove the dependency on `laravel-mix`. Then, you need to require both the PHP package and Vite plugin.

```sh
# Remove Mix
> rm webpack.mix.js
> npm remove laravel-mix

# Require the packages
> composer require innocenzi/laravel-vite
> npm i -D vite laravel-vite
```

Vite's configuration resides in a `vite.config.ts` file at the root of your project. Go ahead and create it, and import `vite-plugin-laravel`.

```ts
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
	plugins: [
		vue(),
		laravel()
	]
})
```

The main configuration is defined in `config/vite.php`, which the Vite plugin reads in order to infer the configuration needed to integrate with Laravel. To configure Vite, publish its configuration:

```sh
> php artisan vendor:publish --tag=vite-config
```

### Matching the Mix config

#### Loaders

If you are using any Webpack loader for TypeScript, Vue, PostCSS, SASS, Stylus... you can safely remove them. Vite handles them without any additionnal configuration. You do need to install the CSS pre-processors you are using though. You can learn more on the [Vite documentation](https://vitejs.dev/guide/features.html#css-pre-processors).

#### Input files

The equivalent of Mix's `.js(input, output)` option is in `config/vite.php`'s entrypoints. For instance, if you have this in your Mix configuration:

```js
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css');
```

The Vite equivalent is defined in `config/vite.php`'s `entrypoints.paths` section:

```php
<?php

return [
  // ...
  'configs' => [
    'default' => [
        'entrypoints' => [
            'paths' => [
                'resources/js/app.js',
                'resources/css/app.css',
            ],
        ],
      // ...
    ],
  ],
];
```

:::tip No output option
You probably noticed the lack of an "output" option in the Vite configuration. That's because Vite builds everything in the configured `build` directory, so you don't need to worry about the paths of your assets.
:::

Going further, the [Vite documentation](https://vitejs.dev) may contain the information you need.
