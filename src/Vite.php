<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;

class Vite
{
    protected ?Manifest $manifest;

    public function __construct(string $manifest = null)
    {
        if (! App::environment('local')) {
            $this->manifest = Manifest::read($manifest);
        }
    }

    /**
     * Gets the script tag for the client module.
     */
    public function getClientScript(): Htmlable
    {
        if (! App::environment('local')) {
            return new HtmlString();
        }

        return $this->getEntry('@vite/client');
    }

    /**
     * Gets the given entry.
     */
    public function getEntry(string $entry): Htmlable
    {
        if (! App::environment('local')) {
            return $this->manifest->getEntry($entry);
        }

        return new HtmlString(\sprintf('<script type="module" src="%s/%s"></script>', \config('vite.dev_url'), $entry));
    }
}
