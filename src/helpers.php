<?php

use Innocenzi\Vite\Vite;

if (! function_exists('vite')) {
    function vite(): Vite
    {
        return app()->make(Vite::class);
    }
}

if (! function_exists('vite_client')) {
    /**
     * Get the HTML script tag that includes the Vite client.
     */
    function vite_client(string $configurationName = null)
    {
        return vite()->config($configurationName)->getClientScriptTag();
    }
}

if (! function_exists('vite_react_refresh_runtime')) {
    /**
     * Get the HTML script tag that includes the React Refresh runtime.
     */
    function vite_react_refresh_runtime(string $configurationName = null)
    {
        return vite()->config($configurationName)->getReactRefreshRuntimeScript();
    }
}

if (! function_exists('vite_tag')) {
    /**
     * Get the HTML tags that include the given entry.
     */
    function vite_tag(string $entry, string $configurationName = null)
    {
        return vite()->config($configurationName)->getTag($entry);
    }
}

if (! function_exists('vite_tags')) {
    /**
     * Get the HTML tags for the Vite client and every configured entrypoint.
     */
    function vite_tags(string $configurationName = null)
    {
        return vite()->config($configurationName)->getTags();
    }
}

if (! function_exists('vite_asset')) {
    /**
     * Gets a valid URL for the given asset.
     */
    function vite_asset(string $path, string $configurationName = null)
    {
        return vite()->config($configurationName)->getAssetUrl($path);
    }
}
