# Troubleshooting

Here are solutions to common problems encountered with either Vite or Laravel Vite. If you encounter an issue that is not mentionned here, feel free to [open a discussion](https://github.com/innocenzi/laravel-vite/discussions).

## The manifest could not be found

This means that Laravel Vite is trying to use a manifest file instead of communicating directly with the development server. This could happen in development if your environment is not set up correctly.

#### In development

- Make sur `APP_ENV` is set to `local`.
- Make sure you [started the development server](./usage#development) with `vite` (`npm run dev`).
- Depending on your local environment, you may want to set [`ping_timeout`](./configuration#ping-timeout) to `false` in `config/vite.php` to disable the [automatic development server discovery](./configuration#automatic-development-server-discovery).

#### In production

- Make sure you generated the manifest with `vite build` (`npm run build`).
- Make sure the manifest has been generated in [`build_path`](./configuration#build_path).

## Local `https` doesn't work

Setting up `https` locally requires a few settings. The development server must use `https` if your application uses it in order to make HMR work, because the websocket will use the `wss` protocol, which needs a secure context.

- Ensure Vite knows about your SSL certificates (see [`withCertificates`](./configuration#ssl-certificates)).
- Ensure `dev_url` in `config/vite.php` is using the `https` protocol.
- Manually visit `https://localhost:3000` (or whatever your `dev_url` is set to) in your browser to approve the self-signed certificate.

## Imported assets don't load in the local environment

This is a known issue caused by Vite stripping the hostname of the base URL when developping locally. A workaround would be to add a fallback route: 

```php
// Workaround for https://github.com/vitejs/vite/issues/2196
Route::fallback(function (string $path) {
    if (! App::environment('local') || ! str_starts_with($path, 'resources')) {
        throw new NotFoundHttpException();
    }

    return Redirect::to(config('vite.dev_url') . '/' . $path);
});

```

You can see more [here](./usage#vite-processed-assets). 
