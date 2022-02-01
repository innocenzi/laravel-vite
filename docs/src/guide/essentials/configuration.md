---
title: Configuration
outline: deep
---

# Configuration

## Configuration types

There is a distinction between configuring Vite and configuring Laravel Vite. 

The former is done in the `vite.config.ts` file: that's where you define Vite and Rollup plugins, for instance. For more information about that, you can refer to the [Vite documentation](https://vitejs.dev/config/). 

:::tip Necessary options
Note that some of these options, especially within the `server` and `build` sections, are defined by the `vite-plugin-laravel` plugin, and are necessary for it to work properly. Be careful when overriding them.
:::

The latter is done in `config/vite.php`, where you can set up aliases, commands that run before starting Vite, and [define other configurations](/guide/extra-topics/multiple-configurations).

&nbsp;

---

:::info Check out the options on their respective documentation pages:
- [Laravel package configuration](/configuration/laravel-package)
- [Vite plugin configuration](/configuration/vite-plugin)
:::
