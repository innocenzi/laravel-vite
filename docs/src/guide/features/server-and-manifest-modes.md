---
title: Server and manifest modes
---

# Server and manifest modes

## Heartbeat check

By default, when generating script or stylesheet tags, Laravel Vite will try to contact the development server to ensure it is started, in which case it will serve the assets.

Otherwise, the manifest file and the previously-generated (and potentially outdated) assets will be used, so the application can still be accessed.

## Conditions

Depending on a few parameters, the heartbeat can be skipped.

- If the application is in production mode (through `APP_ENV`), the development server will never be used.
- If the development server is disabled (through [`dev_server.enabled`](/configuration/laravel-package#enabled)), the development server will not be used.
- If the heartbeat check is disabled (through [`dev_server.ping_before_using_manifest`](/configuration/laravel-package#ping-before-using-manifest)), the development server will always be used.

:::tip Development workflow
When trying to generate tags while the server is not started and the assets are not built, a `ManifestNotFoundException` will be thrown.

Check the [development](/guide/essentials/development) documentation for more information.
:::

## Heartbeat service

You can override the heartbeat service by replacing the `Innocenzi\Vite\HeartbeatCheckers\HeartbeatChecker` implementation. This can be done in the [configuration](/configuration/laravel-package#server-checker).

The default one, `Innocenzi\Vite\HeartbeatCheckers\HttpHeartbeatChecker`, uses Laravel's `Http` client to ping the development server.
