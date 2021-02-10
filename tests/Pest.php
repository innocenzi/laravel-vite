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
use Innocenzi\Vite\Vite;

uses(Innocenzi\Vite\Tests\TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

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
