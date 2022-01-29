<?php

namespace Innocenzi\Vite\EntrypointsFinder;

use Illuminate\Support\Collection;

interface EntrypointsFinder
{
    /**
     * Finds entrypoints.
     *
     * @param array $paths Paths to files or directories that contain entrypoints.
     * @param string $ignore Regular expression to match againsts paths.
     */
    public function find(string|array $paths, string|array $ignore): Collection;
}
