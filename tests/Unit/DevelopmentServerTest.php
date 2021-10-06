<?php

use Innocenzi\Vite\Exceptions\ManifestNotFound;

beforeEach(fn () => set_env('local'));

it('uses the development server if it is started in a local environment', function () {
    with_dev_server(function () {
        expect(get_vite('unknown-manifest.json')->getClientAndEntrypointTags())
            ->toEqual('<script type="module" src="http://localhost:3000/@vite/client"></script>');
    });
});

it('does not use the development server if it is not started in a local environment', function () {
    get_vite('unknown-manifest.json')->getClientAndEntrypointTags();
})->throws(ManifestNotFound::class);

it('generates the right asset URL when the development server is running', function () {
    with_dev_server(function () {
        expect(vite_asset('image.png'))->toBe('http://localhost:3000/image.png');
    });
});

it('generates the right asset URL when the development server is not running', function () {
    expect(vite_asset('image.png'))->toBe('http://localhost/build/image.png');
});
