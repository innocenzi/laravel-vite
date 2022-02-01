---
title: Laravel package configuration
outline: deep
---

# Laravel package

This section explains each configuration option available in the `config/vite.php` configuration file.

As a reminder, you can publish the configuration using the following command:

```sh
> php artisan vendor:publish --tag=vite-config
```

## `configs`

This option is an array of configurations, each one specific to a Vite configuration file. Most of the time, only one configuration is needed, the `default` one. 

But sometimes, you may need to have multiple bundles, in which case you can define multiple configurations. You can learn more about that in the [related documentation](/guide/multiple-configurations).

### `entrypoints`

#### `paths`

This property is required and defines the paths to the entrypoints for this configuration. It can be a single string or an array of string, and either of these can be a path to a file or a directory. Only script files and CSS are supported as entrypoints. 

When using a directory instead of a path to a file, the paths in that directory will all be defined as an entrypoint. When possible, we recommend using a path to a script directly.

#### `ssr`

This property is optional and defines the path to an SSR entrypoint. For more information about that, refer to the [SSR documentation](/guide/features/ssr).

#### `ignore`

This property is optional and defines a regular expression used to filter out entrypoints defined by `paths`.

### `dev_server`

#### `url`

Defines the URL of the development server. For more information about this, see the [development guide](/guide/essentials/development).

#### `ping_before_using_manifest`

Defines if the "fall back to using the manifest" feature should be used. When set to `false`, the manifest will never be used unless the environment is set to `production`.

Read more about this in the [server and manifest modes documentation](/guide/features/server-and-manifest-modes).

#### `ping_url`

Defines an URL to ping in order to reach the development server. Usually, this is not needed and `url` will be used. It may be useful when you have a network configuration in which your browser can reach the development server but the PHP package cannot.

#### `ping_timeout`

Defines the duration, in seconds, to wait before considering that the ping to the development server failed and to use the manifest instead.

#### `cert` and `key`

Defines the path to your SSL certificate and its private key. This is only needed when using `https` and can be auto-discovered when using Valet or Laragon.

Learn more about using `https` in the [development documentation](/guide/essentials/development#using-http-over-tsl).


#### `enabled`

If, for some reason, you don't want to use the development server at all, you can set this option to false. In this case, the manifest will always be used.

### `build_path`

Defines the directory, relative to `/public`, in which the assets should be built. This cannot be an empty string, otherwise the `/public` directory would be emptied.

:::tip Properly name the build paths
When using just one configuration, the default is `build`. When using multiple configurations, we recommend using a directory named after the configuration. For instance, `build/front-office` and `build/back-office`.
:::

## `aliases`

Aliases may be defined in order to avoid using relative paths when importing components and other assets. 

This option is an array of `symbol => path` definitions that takes care of registering aliases and updating the `tsconfig.json` file, which your IDE needs to provide autocompletion.

## `commands`

It's often needed to perform specific operations just before the development server or the bundling starts. This option can define commands to run when that happens.


### `artisan`

Commands defined there will be ran as Artisan commands. When using keyed arrays, the value should be an array of arguments:

```php
'commands' => [
    'artisan' => [
        'ts:generate' => ['--path', 'resources/scripts/types/definition.d.ts']
    ],
],
```

### `shell`

Commands defined here follow the same rules as Artisan commands, except the key can be any valid shell command.

## `testing`

### `use_manifest`

Depending on the way you are testing your application, you may or may not need to use the manifest. 

This option controls whether Laravel Vite should try to read the manifest or generate tags pointing the the development server when in the `testing` environment.

By default, this is disabled to not trigger the `ManifestNotFound` exception when rendering a Blade view.

:::tip Testing tip
You can use `Vite::useManifest()` to change this option on the fly when testing. It optionally accepts a boolean argument.
:::

## `env_prefixes`

This option defines the prefixes that environment variables must have in order to be accessible from the front-end.

By default, environment variables starting with `MIX_`, `VITE_` or `SCRIPT_` are available to the scripts via `import.meta.env`.


:::tip Vite tip
Vite has more features regarding `import.meta.env`, which you can read about in [their documentation](https://vitejs.dev/guide/env-and-mode.html#env-variables-and-modes).
:::

## `interfaces`

### `tag_generator`

This interface covers the logic for generating `script` and `link` tags. It can be useful to override it in case you need to add attributes such as `crossorigin` or `defer`.

Read more about that in the [tag generation documentation](/guide/tag-generation).

### `heartbeat_checker`

This interface covers the logic for checking if the development server is reacheable and should be used. By default, Laravel's `Http` client is used to perform a `GET` request on `/@vite/client`, which should return a `HTTP 200` code.

### `entrypoints_finder`

This interface covers the logic for finding entrypoints from an array of paths. You probably don't need to override it, unless you are using a custom architecture.

## `default`

This option defines the name of the default configuration. This is only useful to change when trying to name configurations according to their scopes, but in most cases, this is not needed.
