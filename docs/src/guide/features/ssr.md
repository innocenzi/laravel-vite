---
title: SSR
---

# SSR

:::warning Experimental
This guide is about an experimental feature that has yet to be tested thoroughly. Feel free to help filling the gaps by contributing to the documentation or the implementation.
:::

## Overview

Vite requires you to implement your own server, with something like [Fastify](https://www.fastify.io/) or [Express](https://expressjs.com).

Instead of running the classic Vite development server using `vite dev`, you would typically start your own with `node resources/ssr/server.js` or `esno resources/ssr/server.ts`. The details are explained within [Vite's SSR documentation](https://vitejs.dev/guide/ssr.html#setting-up-the-dev-server).

## Building

Laravel Vite provides a [per-configuration `entrypoints.ssr` option](/configuration/laravel-package#ssr) to specify a server entrypoint. For instance, it can be `resources/scripts/ssr.ts`. 

Keep in mind that the code in this file should not access the DOM, since it will be executed by Node.

To build the server script, simply use the `--ssr` flag. The Vite plugin will read the configured entrypoint and use it.

:::tip SSR options
You may provide SSR options to the `laravel` plugin. Refer to the [plugin configuration](/configuration/vite-plugin#ssr) documentation to learn more.
:::


### Example `package.json`

As an example, the scripts in `package.json` could look like the following:

```json
{
  "scripts": {
    "dev": "node resources/ssr/server.js",
    "build:client": "vite build",
    "build:server": "vite build --ssr"
  }
}
```
