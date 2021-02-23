<?php

namespace Innocenzi\Vite\Exceptions;

class NoSuchEntrypointException extends \Exception
{
    public function __construct(string $entry)
    {
        $this->message = "'${entry}' does not exist in the manifest. Make sure you added an entry point in the Vite configuration.";
    }
}
