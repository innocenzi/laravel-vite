<?php

namespace Innocenzi\Vite\EntrypointsFinder;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

final class DefaultEntrypointsFinder implements EntrypointsFinder
{
    public function find(string|array $paths, string|array $ignore): Collection
    {
        return collect($paths)
            ->flatMap(function (string $fileOrDirectory) {
                if (!file_exists($fileOrDirectory)) {
                    $fileOrDirectory = base_path($fileOrDirectory);
                }

                if (!file_exists($fileOrDirectory)) {
                    return [];
                }

                if (is_dir($fileOrDirectory)) {
                    return File::files($fileOrDirectory);
                }
                
                return [new \SplFileInfo($fileOrDirectory)];
            })
            ->unique(fn (\SplFileInfo $file) => $file->getPathname())
            ->filter(function (\SplFileInfo $file) use ($ignore) {
                return !collect($ignore)->some(fn ($pattern) => preg_match($pattern, $file->getFilename()));
            });
    }
}
