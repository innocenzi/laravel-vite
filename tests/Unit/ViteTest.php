<?php

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Innocenzi\Vite\Exceptions\NoSuchEntrypointException;

beforeEach(fn () => Http::fake(['/@vite/client' => Http::response()]));

it('generates the client script in a local environment', function () {
    set_env('local');
    expect(get_vite()->getClientScript())
        ->toEqual('<script type="module" src="http://localhost:3000/@vite/client"></script>');
});

it('generates an entry script in a local environment', function () {
    set_env('local');
    expect(get_vite()->getEntry('some/path/script.ts'))
        ->toEqual('<script type="module" src="http://localhost:3000/some/path/script.ts"></script>');
});

it('throws when generating a non-existing entry script in a production environment', function () {
    set_env('production');
    get_vite()->getEntry('some/path/script.ts');
})->throws(NoSuchEntrypointException::class);

it('generates an entry script in a production environment', function () {
    set_env('production');
    expect(get_vite()->getEntry('resources/js/app.js'))
        ->toEqual('<script type="module" src="http://localhost/build/app.83b2e884.js"></script>');
});

it('generates scripts and css from an entry point in a production environment', function () {
    set_env('production');
    expect(get_vite('with_css.json')->getEntry('resources/js/app.js'))
        ->toEqual('<script type="module" src="http://localhost/build/app.83b2e884.js"></script><link rel="stylesheet" href="http://localhost/build/app.e33dabbf.css" />');
});

it('finds an entrypoint by its name when its directory is registered in the configuration in a local environment', function () {
    set_env('local');
    Config::set('vite.entrypoints', 'scripts');
    App::setBasePath(__DIR__);
    expect(get_vite('entry.json')->getEntry('entry.ts'))
        ->toEqual('<script type="module" src="http://localhost:3000/scripts/entry.ts"></script>');
});

it('finds an entrypoint by its name when its directory is registered in the configuration in a production environment', function () {
    set_env('production');
    Config::set('vite.entrypoints', 'scripts');
    App::setBasePath(__DIR__);
    expect(get_vite('entry.json')->getEntry('entry.ts'))
        ->toEqual('<script type="module" src="http://localhost/build/entry.2615a355.js"></script>');
});

it('finds every entrypoints and generates their tags along with the client in a development environment', function () {
    set_env('local');
    Config::set('vite.entrypoints', 'scripts');
    App::setBasePath(__DIR__);
    expect(get_vite()->getClientAndEntrypointTags())
        ->toEqual(implode('', [
            '<script type="module" src="http://localhost:3000/@vite/client"></script>',
            '<script type="module" src="http://localhost:3000/scripts/entry.ts"></script>',
        ]));
});

it('finds a specific entrypoint by path from the configuration', function () {
    set_env('local');
    Config::set('vite.entrypoints', 'scripts-dts/entry.ts');
    App::setBasePath(__DIR__);
    expect(get_vite()->getClientAndEntrypointTags())
        ->toEqual(implode('', [
            '<script type="module" src="http://localhost:3000/@vite/client"></script>',
            '<script type="module" src="http://localhost:3000/scripts-dts/entry.ts"></script>',
        ]));
});

it('filters out entrypoints from the configuration', function () {
    set_env('local');
    Config::set('vite.entrypoints', 'scripts-dts');
    Config::set('vite.ignore_pattern', '');
    App::setBasePath(__DIR__);
    expect(get_vite()->getClientAndEntrypointTags())
        ->toEqual(implode('', [
            '<script type="module" src="http://localhost:3000/@vite/client"></script>',
            '<script type="module" src="http://localhost:3000/scripts-dts/entry.ts"></script>',
        ]));
});

it('does not generate client script tag in production environment', function () {
    set_env('production');
    Config::set('vite.entrypoints', 'scripts');
    App::setBasePath(__DIR__);
    expect(get_vite()->getClientAndEntrypointTags())
        ->toEqual('<script type="module" src="http://localhost/build/app.83b2e884.js"></script>');
});

it('generates production URLs that take the ASSET_URL environment variable into account', function () {
    app()->singleton('url', fn () => new UrlGenerator(
        new RouteCollection(),
        new Request(),
        'https://cdn.random.url'
    ));

    Config::set('vite.entrypoints', 'scripts');
    App::setBasePath(__DIR__);
    expect(get_vite()->getClientAndEntrypointTags())
        ->toEqual('<script type="module" src="https://cdn.random.url/build/app.83b2e884.js"></script>');
});
