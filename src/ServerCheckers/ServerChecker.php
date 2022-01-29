<?php

namespace Innocenzi\Vite\ServerCheckers;

interface ServerChecker
{
    public function ping(string $url, int $timeout): bool;
}
