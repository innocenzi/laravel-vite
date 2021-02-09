<?php

namespace Innocenzi\Vite\Tests;

use Innocenzi\Vite\ViteServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ViteServiceProvider::class,
        ];
    }
}
