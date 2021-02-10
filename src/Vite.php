<?php

namespace Innocenzi\Vite;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

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

        // Try to find a file in the entry points that corresponds to
        // this entry, to avoid having to specify the entire path
        /** @var SplFileInfo $file */
        $file = collect(\config('vite.entrypoints'))
            ->map(fn ($directory) => \base_path($directory))
            ->filter(fn ($directory) => File::isDirectory($directory))
            ->map(fn ($directory) => File::files($directory))
            ->flatten()
            ->first(fn (SplFileInfo $file) => Str::contains($file->getFilename(), $entry));

        // Converts the file into a path that can be
        // understood by Vite
        $file = Str::of($file?->getPathname())
            ->replace(\base_path(), '')
            ->replace('\\', '/')
            ->ltrim('/');

        return new HtmlString(\sprintf('<script type="module" src="%s/%s"></script>', \config('vite.dev_url'), $file->isEmpty() ? $entry : $file));
    }
}
