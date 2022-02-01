<?php

use Innocenzi\Vite\Vite;

it('uses the development server by default', function () {
    with_dev_server();
    set_env('local');
    expect(vite()->usesServer())->toBeTrue();
});

it('uses uses the development server in tests by default', function () {
    expect(vite()->usesServer())->toBeTrue();
});

it('uses uses the manifest in tests when instructed', function () {
    with_dev_server();

    config()->set('vite.testing.use_manifest', true);
    expect(vite()->usesManifest())->toBeTrue();
    
    config()->set('vite.testing.use_manifest', false);
    expect(vite()->usesManifest())->toBeFalse();

    Vite::useManifest();
    expect(vite()->usesManifest())->toBeTrue();
});

it('uses the manifest when the development server is disabled', function () {
    with_dev_server();
    set_env('local');
    set_vite_config('default', [
        'dev_server' => [
            'enabled' => false,
        ],
    ]);
    
    expect(vite()->usesManifest())->toBeTrue();
});

it('uses the manifest when the development server is unreachable', function () {
    with_dev_server(reacheable: false);
    set_env('local');
    expect(vite()->usesManifest())->toBeTrue();
});

it('uses the manifest in production', function () {
    set_env('production');
    expect(vite()->usesManifest())->toBeTrue();
});

it('uses the manifest in production even if a server is reacheable', function () {
    with_dev_server();
    set_env('production');
    expect(vite()->usesManifest())->toBeTrue();
});

it('generates the Vite client script tag', function () {
    with_dev_server();
    set_env('local');
    expect(vite()->getClientScriptTag())
        ->toBe('<script type="module" src="http://localhost:3000/@vite/client"></script>');
});

it('generates the Vite client script tag with the other tags', function () {
    with_dev_server();
    set_fixtures_path('');
    set_env('local');
    set_vite_config('default', [
        'entrypoints' => [
            'paths' => 'entrypoints/multiple',
        ],
    ]);
    
    expect(vite()->getTags())
        ->toContain('<script type="module" src="http://localhost:3000/@vite/client"></script>')
        ->toContain('<script type="module" src="http://localhost:3000/entrypoints/multiple/main.ts"></script>')
        ->toContain('<script type="module" src="http://localhost:3000/entrypoints/multiple/secondary.ts"></script>');
});
