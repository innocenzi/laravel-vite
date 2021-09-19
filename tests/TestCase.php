<?php

namespace Innocenzi\Vite\Tests;

use Innocenzi\Vite\ViteServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Symfony\Component\Process\Process;

class TestCase extends Orchestra
{
    public ?Process $server = null;

    protected function getPackageProviders($app)
    {
        return [
            ViteServiceProvider::class,
        ];
    }
}
