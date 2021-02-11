<?php

namespace Innocenzi\Vite\Exceptions;

class ManifestNotFound extends \Exception
{
    public function __construct(string $path)
    {
        $this->message = \sprintf('The manifest could not be found. Did you run the build command? Tried: %s', $path);
    }
}
