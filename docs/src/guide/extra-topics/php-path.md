---
title: PHP path
---

# PHP path

## Overview

The Vite plugin infers its configuration from what is defined in `config/vite.php`. By default, in order to do that, it will try to call the `php` binary.

In multiple scenarios though, a global `php` binary is not available. In such cases, you will need to configure Vite accordingly.

## Changing the path to PHP

One option is to simply configure Vite to find your `php` binary. There are two ways to do that:
- By setting the `PHP_EXECUTABLE_PATH` environment variable
- By setting the [`php` plugin option](/configuration/vite-plugin.html#php)

Basically, if the `php` plugin option is defined, Vite will use it. Otherwise, it will try to reach for a path defined in `PHP_EXECUTABLE_PATH`, or fall back to calling `php` directly.

## Setting the path to a JSON configuration file

Another option is to preemptively extract the configuration to a JSON file, so the Vite configuration can be defined without calling PHP at all.

The hidden `php artisan vite:config` command outputs a JSON string that you can save wherever you want. 

There are two ways to tell Vite to use your JSON configuration file:
- By setting the `CONFIG_PATH_VITE` environment variable
- By setting the [`config` plugin option](/configuration/vite-plugin.html#config)

If `CONFIG_PATH_VITE` is defined and is a path, Vite will try to read the corresponding file. Otherwise, if `config` is defined, it will be used instead. If neither are defined, the `php` binary will be called according to the rules stated above.
