---
title: Troubleshooting
---

# Troubleshooting

## The manifest could not be found

This exception means that Laravel Vite is trying to use a manifest file and it can't find it. 

If this happens in your local environment, make sure you read about the [server and manifest modes](/guide/essentials/server-and-manifest-modes) and check:
- That you [started the development server](/guide/essentials/development#with-laravel)
- That your `APP_ENV` is set to `local`
- That your configuration's `dev_server.enabled` option is set to `true`
- That your configuration's `dev_server.url` or `dev_server.ping_url` is reacheable via your Laravel application
- If you have your own `HeartbeatChecker` implementation, make sure you defined it in `interfaces.heartbeat_checker`

If this happens in production, make sure you [generated the bundle](/guide/essentials/building-for-production) and that your configuration's `build_path` is correct.

## The page is refreshing in a loop

This is due to the client's websocket not being able to connect to the development server.

It may happen if you are using `https` and your certificates are not properly configured. See [how to use HTTP over TSL](/guide/essentials/development.html#using-http-over-tsl).

## How do I get the path of an asset from the back-end?

There is unfortunately no way to get the path of a Vite-processed asset (eg. an image that was imported in a Vue single-file component) from the back-end.

This is due to the manifest file not containing a reference to the original file path, hence making it impossible to reference an asset both in development and in production.

:::info That issue known
Changing this behavior would be a breaking change. Keep track of the issue in the `vitejs/vite` repository: https://github.com/vitejs/vite/issues/2375.
:::
