---
title: Directives
---

# Directives

Directives help you inject tags in your Blade file. Typically, you will only need to use `@vite`, which is smart enough to include the development server script and your entrypoints. However, `@tag` and `@client` are also available if you need more control.

## `@vite`

This directive prints the tag that includes the development server's script (when necessary) and all of the registered entrypoints, script and CSS. 

If you need to specify a configuration, you can pass its name as the first parameter. The corresponding development server URL will be used.

### Example

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @vite
  <!-- Or: @vite('my-config-name') -->
</head>
<body>
  <!--  -->
</body>
</html>
```

## `@tag`

This directive includes the tag for the entrypoint given as the first parameter. The included entrypoint will be the first of the configured ones that contain the given name.

 If you need to specify a configuration, you can pass its name as the second parameter.

### Example

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @client <!-- don't forget to include the Vite client script -->
  @tag('main')
</head>
<body>
  <!--  -->
</body>
</html>
```

## `@client`

This directive includes the tag for the Vite client script, which is required to provide features such as [HMR](https://vitejs.dev/guide/features.html#hot-module-replacement). It should be included before any other directive. 

When in [manifest mode](/guide/features/server-and-manifest-modes), even if this directive is called, the client script will not be included.

:::tip Render once
You can use the [built-in `@once` directive](https://laravel.com/docs/master/blade#the-once-directive) to render the client script only once if required. If you forget to do that, no worries, the Vite client is smart enough to not initialize twice.
:::

## `@react`

This directive includes the script necessary for providing the hot module replacement feature [when using React](https://vitejs.dev/guide/backend-integration.html). It should be included manually when using React, whether `@vite` is used or not.
