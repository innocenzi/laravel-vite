<?php

namespace Innocenzi\Vite\Exceptions;

use Illuminate\Support\Facades\App;

class ManifestNotFound extends \Exception
{
    public function __construct(string $path)
    {
        $hint = App::environment('local')
            ? 'Did you start the development server?'
            : 'Did you run the build command?';

        $this->message = sprintf('The manifest could not be found. %s Tried: %s', $hint, $path);
    }
}
