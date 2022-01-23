---
title: Installation
editLink: true
---

# Installation

## Using the preset

This is the recommended and the easiest approach. The [preset](https://github.com/laravel-presets/vite) can be applied with a single command, without prior installation.

### Inertia

If you are an [Inertia](https://inertiajs.com/) user, you may use [an updated but not-yet-documented version of Preset for Inertia](https://github.com/laravel-presets/inertia/blob/main/preset.ts).

```bash
npx @preset/cli apply laravel:inertia
```

This preset will install Vite along with Inertia, Vue and Tailwind CSS. The later can be not installed by using the `--no-tailwindcss` flag.

### Vanilla

If you just want Vite, you can run the following command at the root of your project:

```bash
npx apply laravel:vite --ignore-existing
```

The preset defaults to installing Vue 3. If you don't want that, you may use the `--no-vue` flag.

The preset will:

- Delete `resources/js` and create `resources/scripts`
- Delete `webpack.mix.js` and remove the dependency to `laravel-mix`
- Add development dependencies on `vite` and `laravel-vite` and update `package.json`'s scripts
- Add a dependency on [`innocenzi/laravel-vite`](https://github.com/innocenzi/laravel-vite)
- Create a `vite.config.ts` configuration file
- Add a call to the [`@vite`](./usage#vite) directive in `welcome.blade.php`

## Manually

If you'd rather install Vite manually, you can follow these steps.

First, you need to remove Laravel Mix. You can do that by deleting `webpack.mix.js` and removing the dependency on `laravel-mix`. Then, you need to require both the PHP and the Node packages.

```bash
# Remove Mix
rm webpack.mix.js
yarn remove laravel-mix

# Require the packages
composer require innocenzi/laravel-vite
yarn add vite laravel-vite --dev
```

Vite is configured via a `vite.config.ts` file at the root of your project. Laravel Vite intervenes at this step to integrate with Laravel. For more information about that, read the [configuration documentation](/guide/configuration).

```ts
// vite.config.ts
import { defineConfig } from 'laravel-vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig()
	.withPlugin(vue)
	.merge({
		// Your own Vite options
	})
```

Edit your `package.json` file's `scripts` property:

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "serve": "vite preview"
}
```

Finally, add the [`@vite`](./usage#vite) directive to your Blade files.
