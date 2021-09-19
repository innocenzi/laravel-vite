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
use Innocenzi\Vite\Vite;
use Pest\TestSuite;
use Symfony\Component\Process\Process;

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
    App::bind('env', fn () => $env);
}

function get_vite(string $manifest = 'manifest.json'): Vite
{
    return new Vite(__DIR__ . "/Unit/manifests/${manifest}");
}

/**
 * Creates a sandbox in which the base path is updated.
 */
function sandbox(callable $callback, string $base = __DIR__): string
{
    return tap($base . '/__sandbox__/' . Str::random(), function (string $directory) use ($callback) {
        $initialBasePath = base_path();
        App::setBasePath($directory);
        File::makeDirectory($directory, 0755, true);
        $callback($directory);
        File::deleteDirectory($directory);
        App::setBasePath($initialBasePath);
    });
}

/**
 * Starts the fake development server.
 */
function start_dev_server(): void
{
    if (optional(this()->server)->isRunning()) {
        return;
    }

    this()->server = new Process([
        'php',
        '-S',
        'localhost:3000',
        $directory = __DIR__ . '/Server',
        $directory . '/index.php',
    ]);

    this()->server->start();
    this()->server->waitUntil(fn ($_, $output) => str_contains($output, 'started'));
}

/**
 * Stops the fake development server.
 */
function stop_dev_server(): void
{
    if (optional(this()->server)->isRunning()) {
        this()->server->stop();
    }
}

/**
 * Calls the given callback after a fake development server has been started.
 */
function with_dev_server(callable $callback): void
{
    start_dev_server();
    $callback();
    stop_dev_server();
}
