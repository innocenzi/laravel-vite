---
title: Production
editLink: true
---

# Production

## Building for production

Running the `build` script will generate production-ready assets through Vite. Their linking is handled by the [directives](/guides/usage#directives), so no further action is required besides setting your `APP_ENV` to something else than `local`.

By default, assets are built in `public/build`. If you used Laravel Mix, you may be used to have them generated in `public` directly. With Vite, this isn't directly possible since the build directory is emptied. I believe this restriction is great, as having the assets in their own directory makes it easier to work with them.

You can change the build path in the [configuration](/guide/configuration), but it is best to keep the default, unless you have a specific requirement.

## `ASSET_URL` environment variable

Both this package's [`vite_asset`](/guide/usage#assets) and Laravel's default `asset` helpers make use of the `ASSET_URL` environment variable to generate an asset link.

This is particularly useful if assets are stored in a cloud-based storage such as S3, which is the case with [Laravel Vapor](https://docs.vapor.build/1.0/projects/deployments.html#assets).
