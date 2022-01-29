<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Innocenzi\Vite\Tests\TestCase;
use Pest\TestSuite;

uses(Innocenzi\Vite\Tests\TestCase::class)->in('Unit');
uses(Innocenzi\Vite\Tests\TestCase::class)->in('Features');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Gets the current test case.
 */
function this(): TestCase
{
    return TestSuite::getInstance()->test;
}

/**
 * Sets the environment.
 */
function set_env(string $env): void
{
    app()->bind('env', fn () => $env);
}

/**
 * Creates a sandbox in which the base path is updated.
 */
function in_sandbox(callable $callback, string $base = __DIR__): string
{
    return tap($base . '/__sandbox__/' . Str::random(), function (string $directory) use ($callback) {
        $initialBasePath = base_path();
        App::setBasePath($directory);
        File::makeDirectory($directory, recursive: true);
        $callback($directory);
        File::deleteDirectory($directory);
        App::setBasePath($initialBasePath);
    });
}
