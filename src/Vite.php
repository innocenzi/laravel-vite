<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Facades\App;

class Vite
{
    protected ?Manifest $manifest;

    public function __construct()
    {
        if (! App::environment('local')) {
            $this->manifest = Manifest::read();
        }
    }

    /**
     * Gets the script tag for the client module.
     */
    public function getClientScript(): string
    {
        if (! App::environment('local')) {
            return '';
        }

        return $this->getEntry('@vite/client');
    }

    /**
     * Gets the given entry.
     */
    public function getEntry(string $entry): string
    {
        if (! App::environment('local')) {
            return $this->manifest->getEntry($entry);
        }

        return \sprintf('<script type="module" src="%s/%s"></script>', \config('vite.dev_url'), $entry);
    }
}
