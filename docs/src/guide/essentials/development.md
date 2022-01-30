---
title: Development
---

# Development

Developing with Vite might be a bit different from what you may be used to in the Webpack world. To be more specific, Vite isn't a bundler. Rather, it's a pre-configured tooling pipeline that uses [Rollup](https://rollupjs.org/) for building and a local server for development.

When you start Vite's development server, [pre-optimisations](https://vitejs.dev/guide/dep-pre-bundling.html) are made on some of your dependencies. After that, your assets are being served - there is no bundling or compiling needed at that point, so starting the server is very fast.

## With Laravel

Simply start the development server:

```sh
> npm run dev
```

Using the default configuration, the server would be serving at `http://localhost:3000`.
Paths in your scripts, CSS or import statements will automatically be handled by Vite to point there, so you don't have to worry about it. You can keep using relative paths and aliases.

:::warning This server is not your application server
Its sole purpose is to serve assets, such as scripts or stylesheets. It is not where your application is served: you still need to use something like [Valet](https://laravel.com/docs/8.x/valet), [Laragon](https://laragon.org/), [Docker](https://www.docker.com/) or `php artisan serve`.
:::

## Using HTTP over TSL

If you are using `https` locally, which you should, you will need to update the development server's URL to use the `https` protocol, and you will need to fill your environment variables with paths to your certificates.

With the default configuration, this means setting up, in your `.env`:
- `DEV_SERVER_URL`, for instance `https://localhost:300`
- `DEV_SERVER_KEY`, for instance `C:/laragon/etc/ssl/laragon.key`
- `DEV_SERVER_CERT`, for instance `C:/laragon/etc/ssl/laragon.crt`

These are the default environment variables, but you can change them on a per-configuration basis in `config/vite.php`.

:::tip Using Valet or Laragon?
You may not need to fill the `DEV_SERVER_KEY` and `DEV_SERVER_CERT` environment variables, as they are inferred automatically.
:::
