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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Innocenzi\Vite\Configuration;
use Innocenzi\Vite\Manifest;
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
 * Uses a configuration with the given manifest.
 */
function using_manifest(string $manifest = 'manifest.json'): Configuration
{
    return new Configuration('default', Manifest::read(__DIR__ . "/Unit/manifests/${manifest}"));
}

/**
 * Gets the given manifest.
 */
function get_manifest(string $manifest = 'manifest.json'): Manifest
{
    set_manifest_base_path('');

    return Manifest::read(realpath(__DIR__ . "/Unit/manifests/${manifest}"));
}

/**
 * Overrides the manifests' base paths.
 */
function set_manifest_base_path(string $path)
{
    app()->bind('path.public', fn () => realpath(__DIR__ . "/Unit/manifests/${path}"));
}

/**
 * Sets up a Vite configuration.
 */
function set_vite_config(string $name, array $config): void
{
    config()->set("vite.configs.${name}", array_replace_recursive(config('vite.configs.default'), $config));
}

/**
 * Mocks the dev server.
 */
function with_dev_server(bool $reacheable = true)
{
    if ($reacheable) {
        return Http::fake(fn () => Http::response(status: 404));
    }
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
