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
