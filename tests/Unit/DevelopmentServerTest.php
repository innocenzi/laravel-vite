<?php

use Illuminate\Support\Facades\Http;
use Innocenzi\Vite\Exceptions\ManifestNotFound;

beforeEach(fn () => set_env('local'));

it('uses the development server if it is started in a local environment', function () {
    Http::fake(fn () => Http::response());
    expect(get_vite('unknown-manifest.json')->getClientAndEntrypointTags())
        ->toEqual('<script type="module" src="http://localhost:3000/@vite/client"></script>');
});

it('does not use the development server if it is not started in a local environment', function () {
    Http::fake(fn () => Http::response(null, 404));
    get_vite('unknown-manifest.json')->getClientAndEntrypointTags();
})->throws(ManifestNotFound::class, 'The manifest could not be found. Did you start the development server?');

it('generates the right asset URL when the development server is running', function () {
    Http::fake(fn () => Http::response());
    expect(vite_asset('image.png'))->toBe('http://localhost:3000/image.png');
});

it('generates the right asset URL when the development server is not running', function () {
    Http::fake(fn () => Http::response(null, 404));
    expect(vite_asset('image.png'))->toBe('http://localhost/build/image.png');
});
