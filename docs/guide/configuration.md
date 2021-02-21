---
title: Configuration
editLink: true
---

# Configuration

## Publishing the configuration

The configuration file can be published thanks to the following command:

```bash
php artisan vendor:publish --tag=laravel-vite-config
```

## Options

### `build_path`

- **Default**: `build`

This option configures the directory in which the assets will be built, relative to the `public` directory. It should not be empty, as the build directory is cleared by Vite upon asset generation, which would erase existing public files such as `index.php`.

It is best not to change this option unless you have a specific requirement.

### `dev_url`

- **Default**: `http://localhost:3000`

When using Vite in development, assets must link to the development server. This option is used for this purpose, and will also be injected in Vite's configuration. If, for instance, you have multiple Vite servers running on your machine, you may want to update `dev_url` to `http://localhost:3001`.

### `entrypoints`

- **Default**: `[ 'resources/scripts', 'resources/js' ]`
- **Type**: `string[]` or `false`

This option determines the automatic entrypoints. Scripts in this directory will be automatically added to Vite's configuration and injected to your Blade files through the [`@vite` directive](/guide/development#vite). You can disable this feature by setting the option to `false`.

It is recommended to only use `resources/scripts`, unless you have specific requirements.

### `aliases`

- **Default**: `[ '@' => 'resources' ]`

This option defines aliases that will be added to Vite's configuration. Additionally, in order to support Visual Studio Code, starting the development server while having aliases will update the `tsconfig.json` file (or create one if it does not exist) with the configured aliases.

Note that you can manually generate or update this file with the `vite:aliases` Artisan command.

## Vite configuration file

While most of the configuration can be done within `config/vite.php`, if you need more flexibility, you will need to update `vite.config.ts`.

If you used the preset or following the [installation instructions](/guide#installation), it should look like the following:

```ts
// vite.config.ts
export { default } from "laravel-vite";
```

This is a shortcut that you can keep if you don't need configuration. Otherwise, you need to import the `defineConfig` object and export it as `default` manually, so you can chain methods on it:

```ts
// vite.config.ts
import { defineConfig } from "laravel-vite";

export default defineConfig();
```

This is a small wrapper around Vite's configuration that adds a few convenience methods, such as `withEntry` to add an entrypoint or `withOutput` to change the build directory. These two options are taken care of by Laravel Vite, so you don't need to define them manually.

### Adding plugins

You may call `withPlugin` or `withPlugins` with the plugin as a parameter.

<!-- prettier-ignore -->
```ts
// vite.config.ts
import { defineConfig } from "laravel-vite";
import vue from "@vitejs/plugin-vue";
import components from "vite-plugin-components";

export default defineConfig()
  .withPlugins(vue, components);
```

:::tip
If you don't call the plugin method, it will be done by `withPlugin`, which is why the example above uses `vue` instead of `vue()`.
If you need to pass a configuration object, use the latter.
:::

### Other Vite options

You may pass a Vite configuration object to `defineConfig`, like you would if you imported it from `vite`.

<!-- prettier-ignore -->
```ts
// vite.config.ts
import { defineConfig } from "laravel-vite";

export default defineConfig({
  // Any valid Vite configuration option
  server: {
    open: true
  }
})
```

This will deeply merge the given configuration object, so you don't need to worry about overriding existing entrypoints or plugins this way.
