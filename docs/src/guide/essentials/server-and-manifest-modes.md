---
title: Server and manifest modes
---

# Server and manifest modes

## Modes

### Server mode

By default, when generating script or stylesheet tags, Laravel Vite will try to contact the development server (this is the "heartbeat check") to ensure it is started, in which case it will serve the assets. 

This is what you will typically use in development.

### Manifest mode

Otherwise, the manifest file and the previously-generated (and potentially outdated) build will be used. This allows the application to still be accessible even when the development server is not started.

This happens in production, but there are also a few situations when the manifest mode can be used in a local environment.

## Conditions

Depending on a few parameters, the manifest mode will be used and the heartbeat check can be skipped.

- If the application is in production mode (through `APP_ENV`), the manifest mode will always be used.
- If the development server is disabled (through [`dev_server.enabled`](/configuration/laravel-package#enabled)), the manifest mode will always be used.
- If the heartbeat check is disabled (through [`dev_server.ping_before_using_manifest`](/configuration/laravel-package#ping-before-using-manifest)), the manifest mode will not be used, unless in production.

:::tip Common exception
When trying to generate tags while the server is not started and the assets are not built, a `ManifestNotFoundException` will be thrown.

Check the [development](/guide/essentials/development) documentation for more information.
:::

## Heartbeat check implementation

You may need to replace the heartbeat implementation depending on your networking setup. 

To do so, you will need to bind your own implementation to the  `Innocenzi\Vite\HeartbeatCheckers\HeartbeatChecker` interface. This can be done in the [configuration](/configuration/laravel-package#heartbeat-checker).
