<?php

use Illuminate\Routing\UrlGenerator;
use Innocenzi\Vite\Configuration;
use Innocenzi\Vite\Exceptions\NoBuildPathException;
use Innocenzi\Vite\Exceptions\NoSuchConfigurationException;
use Innocenzi\Vite\Vite;

afterAll(fn () => Vite::$useManifestCallback = null);

it('uses the default configuration when not specifying one', function () {
    expect(vite()->getClientScriptTag())
        ->toEqual('<script type="module" src="http://localhost:3000/@vite/client"></script>');
});

it('uses the right configuration when specifying it', function () {
    set_vite_config('custom', [
        'dev_server' => [
            'url' => 'http://localhost:3001',
        ],
    ]);

    expect(vite('custom')->getConfig('dev_server.url'))->toBe('http://localhost:3001');
});

it('generates URLs relative to the app URL by default in production', function () {
    set_fixtures_path('builds');
    set_env('production');
    
    expect(using_manifest('builds/public/with-css/manifest.json')->getTags())
        ->toContain('<link rel="stylesheet" href="http://localhost/with-css/assets/test.65bd481b.css" />')
        ->toContain('<script type="module" src="http://localhost/with-css/assets/test.a2c636dd.js"></script>');
});

it('generates URLs relative to the configured ASSET_URL in production', function () {
    set_fixtures_path('builds');
    set_env('production');

    $property = new ReflectionProperty(UrlGenerator::class, 'assetRoot');
    $property->setAccessible(true);
    $property->setValue(app('url'), 'https://s3.us-west-2.amazonaws.com/12345678');
    
    expect(using_manifest('builds/public/with-css/manifest.json')->getTags())
        ->toContain('<link rel="stylesheet" href="https://s3.us-west-2.amazonaws.com/12345678/with-css/assets/test.65bd481b.css" />')
        ->toContain('<script type="module" src="https://s3.us-west-2.amazonaws.com/12345678/with-css/assets/test.a2c636dd.js"></script>');
});

it('throws when accessing a configuration that does not exist', function () {
    vite('unknown-configuration')->getClientScriptTag();
})->throws(NoSuchConfigurationException::class);

it('throws when the build path is not defined', function () {
    set_fixtures_path('builds');
    set_env('production');
    set_vite_config('default', [
        'build_path' => '',
    ]);

    vite()->getTags();
})->throws(NoBuildPathException::class);

it('fins the manifest path', function () {
    set_fixtures_path('builds');
    set_env('production');

    set_vite_config('default', ['build_path' => '']);
    expect(vite()->getManifestPath())->toBe(str_replace('\\', '/', public_path('manifest.json')));

    set_vite_config('default', ['build_path' => '/']);
    expect(vite()->getManifestPath())->toBe(str_replace('\\', '/', public_path('manifest.json')));
    
    set_vite_config('default', ['build_path' => '/build']);
    expect(vite()->getManifestPath())->toBe(str_replace('\\', '/', public_path('build/manifest.json')));
    
    set_vite_config('default', ['build_path' => '/build/']);
    expect(vite()->getManifestPath())->toBe(str_replace('\\', '/', public_path('build/manifest.json')));
});

it('finds a configured entrypoint by its name in development', function () {
    with_dev_server();
    set_fixtures_path('');
    set_env('local');
    set_vite_config('default', [
        'entrypoints' => [
            'paths' => 'entrypoints/multiple',
        ],
    ]);

    expect(vite()->getTag('main'))->toContain('http://localhost:3000/entrypoints/multiple/main.ts');
});

it('returns a valid asset URL in development', function () {
    with_dev_server();
    set_env('local');
        
    set_vite_config('default', ['build_path' => '/should-not-be/included']);
    expect(vite()->getAssetUrl('/my-custom-asset.txt'))->toContain('http://localhost:3000/my-custom-asset.txt');
    expect(vite()->getAssetUrl('without-leading-slash.txt'))->toContain('http://localhost:3000/without-leading-slash.txt');
});

it('returns a valid asset URL in production', function () {
    set_env('production');
        
    set_vite_config('default', ['build_path' => '/with/slashes/']);
    expect(vite()->getAssetUrl('/my-custom-asset.txt'))->toContain('http://localhost/with/slashes/my-custom-asset.txt');
    
    set_vite_config('default', ['build_path' => '/with/leading/slash']);
    expect(vite()->getAssetUrl('/my-custom-asset.txt'))->toContain('http://localhost/with/leading/slash/my-custom-asset.txt');
    
    set_vite_config('default', ['build_path' => 'with/trailing/slash/']);
    expect(vite()->getAssetUrl('/my-custom-asset.txt'))->toContain('http://localhost/with/trailing/slash/my-custom-asset.txt');
    
    set_vite_config('default', ['build_path' => 'build']);
    expect(vite()->getAssetUrl('/my-custom-asset.txt'))->toContain('http://localhost/build/my-custom-asset.txt');
    expect(vite()->getAssetUrl('my-custom/asset.txt'))->toContain('http://localhost/build/my-custom/asset.txt');

    $property = new ReflectionProperty(UrlGenerator::class, 'assetRoot');
    $property->setAccessible(true);
    $property->setValue(app('url'), 'https://s3.us-west-2.amazonaws.com/12345678');
    
    expect(vite()->getAssetUrl('/my-custom-asset.txt'))
        ->toContain('https://s3.us-west-2.amazonaws.com/12345678/build/my-custom-asset');
});

it('respects the mode override in production', function () {
    set_env('production');
    
    expect(vite()->usesManifest())->toBeTrue();

    Vite::useManifest(fn () => false);

    expect(vite()->usesManifest())->toBeFalse();
});

it('respects the mode override in development', function () {
    with_dev_server(reacheable: true);
    set_env('local');
    
    expect(vite()->usesManifest())->toBeFalse();

    Vite::useManifest(fn () => true);

    expect(vite()->usesManifest())->toBeTrue();

    config()->set('vite.configs.default.dev_server.enabled', false);

    Vite::useManifest(function (Configuration $cfg) {
        return $cfg->getConfig('dev_server.enabled');
    });

    expect(vite()->usesManifest())->toBeFalse();
});

it('does not override the mode if returning null from the callback', function () {
    set_env('production');
    expect(vite()->usesManifest())->toBeTrue();

    Vite::useManifest(fn () => false);
    expect(vite()->usesManifest())->toBeFalse();
    
    Vite::useManifest(fn () => null);
    expect(vite()->usesManifest())->toBeTrue();
    
    with_dev_server(reacheable: true);
    set_env('local');
    expect(vite()->usesManifest())->toBeFalse();
});
