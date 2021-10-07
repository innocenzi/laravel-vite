---
title: Configuration
editLink: true
---

# Configuration

## Publishing the configuration

The configuration file can be published thanks to the following command:

```bash
php artisan vendor:publish --tag=vite-config
```

## Options

### `entrypoints`

- **Default**: `[ 'resources/scripts', 'resources/js' ]`
- **Type**: `string`, `string[]` or `false`

This option determines the paths to the entrypoints. It should be a list paths to directories or files. Each script will be added to Vite's configuration and will have a corresponding tag generated through the [`@vite` directive](./usage#vite).

You can disable this feature by setting the option to `false`.

It is recommended to only use `resources/scripts`, unless you have specific requirements.

### `ignore_patterns`

- **Defaults**: `["/\.d\.ts$/"]`
- **Type**: `RegExp[]`

This option defines the regular expressions that filter out files found in the `entrypoints` directories. By default, files ending with `.d.ts` will be ignored, so you can have type definitions in your `scripts` directory.

### `aliases`

- **Default**: `[ '@' => 'resources' ]`
- **Type**: `Record<string, string>`

This option defines aliases that will be added to Vite's configuration. Additionally, in order to support Visual Studio Code, starting the development server while having aliases will update the `tsconfig.json` file (or create one if it does not exist) with the configured aliases.

Note that you can manually generate or update this file with the `vite:aliases` Artisan command.

### `public_directory`

- **Default**: `resources/static`
- **Type**: `string`

This option defines the directory that Vite considers as the public directory. Its content will be copied to the build directory at build-time. Note that this should not be set to `public`, otherwise there will be a copy loop.

This option directly maps to Vite's [`publicDir`](https://vitejs.dev/config/#publicdir).

### `build_path`

- **Default**: `build`
- **Type**: `string`

This option configures the directory in which the assets will be built, relative to the `public` directory. It should not be empty, as the build directory is cleared by Vite upon asset generation, which would erase existing public files such as `index.php`.

It is best not to change this option unless you have a specific requirement.

### `dev_url`

- **Default**: `http://localhost:3000`
- **Type**: `string`

When using Vite in development, assets must link to the development server. This option is used for this purpose, and will also be injected in Vite's configuration. If, for instance, you have multiple Vite servers running on your machine, you may want to update `dev_url` to `http://localhost:3001`.

### `ping_timeout`

- **Default**: `10`
- **Type**: `number`

This defines the maximum duration, in seconds, that the ping to the development server should take while trying to determine whether to use the manifest or the server in a local environment.

Setting this value to `null` or `false` will disable the check, so the manifest won't be used at all in a local environment.

### `ping_url`

- **Default**: `null`
- **Type**: `string`

This defines the URL to which Laravel Vite will perform a ping when ensuring that the development server is started. If falsy, `dev_url` will be used.

### `commands`

- **Default**: `['vite:aliases']`
- **Type**: `array`

This option defines the list of artisan commands that will be executed when the development server is starting. Note that if a command fails, the server will just stop. 

## Vite configuration file

While most of the configuration can be done within `config/vite.php`, if you need more flexibility, you will need to update `vite.config.ts`.

If you used the preset or following the [installation instructions](./index#installation), it should look like the following:

```ts
import { defineConfig } from 'laravel-vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig()
	.withPlugin(vue)
	.merge({
		// Your own Vite options
	})
```

Note that `defineConfig` is imported from `laravel-vite`. This is a small wrapper around Vite's configuration that adds a few convenience methods, such as `withEntry` to add an entrypoint or `withOutput` to change the build directory. These two options are taken care of by Laravel Vite, so you don't need to define them manually.

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

### SSL certificates

If your local environment is using the `https` protocol, you'll need to configure Vite's development server to use your SSL certificates. You can do this using `withCertificates`. The parameters are the paths to the `.key` and the `.crt` files, respectively. 

#### Environment-specific certificates

If you share your project between different environments, you can use pass a callback to `withCertificates` and return a tuple which contains the paths to the `.key` and the `.crt`: 

```ts
export default defineConfig()
	.withCertificates((env) => ([
		env.VITE_DEV_KEY,
		env.VITE_DEV_CERT,
	]))
```

Alternatively, using `withCertificates` without argumentse will use the `VITE_DEV_KEY` and `VITE_DEV_CERT` environment variables by default: 

```ts
export default defineConfig()
	.withCertificates()
```

#### Laravel Valet

If you use Laravel Valet, you can use `withValetCertificates`. The domain name will be determined from your `APP_URL` environment variable, but you can override it by passing it as the `domain` property of the first parameter of the method.

```ts
import { defineConfig } from 'laravel-vite'

export default defineConfig()
	.withValetCertificates({ domain: 'my-app.test' })
```

#### Laragon

If you are on Windows using [Laragon](https://laragon.com) (which is highly recommended on Windows for non-Docker and non-WSL environments), you can use `withLaragonCertificates` to pass Laragon's certificates to the development server. 

```ts
import { defineConfig } from 'laravel-vite'

export default defineConfig()
	.withLaragonCertificates()
```

If Laragon is not installed in its default installation directory, `C:/laragon`, you can pass its installation path as the first parameter of `withLaragonCertificates`.

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
