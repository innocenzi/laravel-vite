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
