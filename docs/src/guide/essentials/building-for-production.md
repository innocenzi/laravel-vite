---
title: Building for production
---

# Building for production

Running the `vite build` command will generate production-ready assets through Rollup. 

## Build path

By default, assets are generated in `/public/build`. When using Laravel Mix, they were usually put in the `/public` directory directly. 

With Vite, this is bad practice because the build directory is emptied at build-time. It also has the benefits of making the assets more organized and easier to ignore through `.gitignore`.

If you wish, you can change the build path. See the [related documentation](/configuration/laravel-package.html#build-path).

## `ASSET_URL` environment variable

When bundling, the `ASSET_URL` environment variable is used as the base path for every generated asset. 

This is particularly useful when assets are stored in a cloud-based storage such as S3, which is the case with [Laravel Vapor](https://vapor.laravel.com).
