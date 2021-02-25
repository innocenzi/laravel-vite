<?php

if (! function_exists('vite_client')) {
    /**
     * Get the HTML script tag that includes the Vite client.
     *
     * @return string
     */
    function vite_client()
    {
        return app()->make(Innocenzi\Vite\Vite::class)->getClientScript();
    }
}

if (! function_exists('vite_react_refresh_runtime')) {
    /**
     * Get the HTML script tag that includes the React Refresh runtime.
     *
     * @return string
     */
    function vite_react_refresh_runtime()
    {
        return app()->make(Innocenzi\Vite\Vite::class)->getReactRefreshRuntimeScript();
    }
}

if (! function_exists('vite_entry')) {
    /**
     * Get the HTML tags that include the given entry.
     *
     * @return string
     */
    function vite_entry(string $entry)
    {
        return app()->make(Innocenzi\Vite\Vite::class)->getEntry($entry);
    }
}

if (! function_exists('vite_tags')) {
    /**
     * Get the HTML tags for the Vite client and every configured entrypoint.
     *
     * @return string
     */
    function vite_tags()
    {
        return app()->make(Innocenzi\Vite\Vite::class)->getClientAndEntrypointTags();
    }
}

if (! function_exists('vite_asset')) {
    /**
     * Gets a valid URL for the given asset.
     */
    function vite_asset(string $path)
    {
        return app()->make(Innocenzi\Vite\Vite::class)->getAssetUrl($path);
    }
}
