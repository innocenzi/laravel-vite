---
title: Entrypoints
---

# Entrypoints

## Definition

Entrypoints are scripts or CSS files that Laravel Vite will recognize and use. They are the equivalent of Laravel Mix's `.js` or `.postCss`'s first parameters and are configured in `config/vite.php`. See the [related documentation](/configuration/laravel-package#entrypoints).

Within Laravel Vite, it means they will be passed to Vite and will be injected automatically via the `@vite` directive.

## Usage

Typically, you would only need to use the [`@vite` directive](/guide/features/directives#vite) in a Blade file to include all configured entrypoints. 

When needed, you can use the [`@tag` directive](/guide/features/directives#tag) instead, which gives you more granular control over what is included.
Remember that in this case, you will need to include the Vite client script as well using the [`@client` directive](/guide/features/directives#client).

## Automatic CSS injection

In [manifest mode](/guide/features/server-and-manifest-modes), when using either the `@vite` or `@tag` directives, any CSS file included within the scripts will also be rendered.

In server mode, CSS files will be injected by the Vite client script.

## Flashes of unstyled content

During development, you can encounter a [FOUC](https://en.wikipedia.org/wiki/Flash_of_unstyled_content) depending on the load-time of your page. This is caused by relying on the Vite client to load the CSS included in the scripts.

To resolve this issue, you may use a CSS file as an entrypoint instead of importing it in a script. This will result in its style tag being included by the `@vite` and `@tag` directives instead of waiting for the Vite client to inject them.
