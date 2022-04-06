<?php

use Innocenzi\Vite\Configuration;
use Innocenzi\Vite\Vite;

if (!function_exists('vite')) {
    /**
     * Gets the given Vite configuration instance or the default one.
     */
    function vite(string $config = null): Configuration
    {
        return app()->make(Vite::class)->config($config);
    }
}

if (!function_exists('vite_client')) {
    /**
     * Get the HTML script tag that includes the Vite client.
     */
    function vite_client(string $configurationName = null)
    {
        return vite($configurationName)->getClientScriptTag();
    }
}

if (!function_exists('vite_react_refresh_runtime')) {
    /**
     * Get the HTML script tag that includes the React Refresh runtime.
     */
    function vite_react_refresh_runtime(string $configurationName = null)
    {
        return vite($configurationName)->getReactRefreshRuntimeScript();
    }
}

if (!function_exists('vite_tag')) {
    /**
     * Get the HTML tags which path name include the given entry name.
     */
    function vite_tag(string $entry, string $configurationName = null)
    {
        return vite($configurationName)->getTag($entry);
    }
}

if (!function_exists('vite_tags')) {
    /**
     * Get the HTML tags for the Vite client and every configured entrypoint.
     */
    function vite_tags(string $configurationName = null)
    {
        return vite($configurationName)->getTags();
    }
}

if (!function_exists('vite_asset')) {
    /**
     * Gets a valid URL for the given asset path.
     */
    function vite_asset(string $path, string $configurationName = null)
    {
        return vite($configurationName)->getAssetUrl($path);
    }
}

if (!function_exists('vite_entry')) {
    /**
     * Gets the URL for the given entrypoint.
     */
    function vite_entry(string $path, string $configurationName = null)
    {
        return vite($configurationName)->getEntryUrl($path);
    }
}
