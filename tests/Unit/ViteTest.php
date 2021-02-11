<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Innocenzi\Vite\Exceptions\NoSuchEntrypointException;

it('generates the client script in a local environment', function () {
    set_env('local');
    expect(get_vite()->getClientScript())
        ->toEqual('<script type="module" src="http://localhost:3000/@vite/client"></script>');
});

it('does not generate the client script in a production environment', function () {
    set_env('producton');
    expect(get_vite()->getClientScript())
        ->toEqual('');
});

it('generates an entry script in a local environment', function () {
    set_env('local');
    expect(get_vite()->getEntry('some/path/scrip.ts'))
        ->toEqual('<script type="module" src="http://localhost:3000/some/path/scrip.ts"></script>');
});

it('throws when generating a non-existing entry script in a production environment', function () {
    set_env('production');
    get_vite()->getEntry('some/path/script.ts');
})->throws(NoSuchEntrypointException::class);

it('generates an entry script in a production environment', function () {
    set_env('production');
    expect(get_vite()->getEntry('resources/js/app.js'))
        ->toEqual('<script src="/build/app.83b2e884.js"></script>');
});

it('generates scripts and css from an entry point in a production environment', function () {
    set_env('production');
    expect(get_vite('with_css.json')->getEntry('resources/js/app.js'))
        ->toEqual('<script src="/build/app.83b2e884.js"></script><link rel="stylesheet" href="/build/app.e33dabbf.css" />');
});

it('finds an entrypoint by its name when its directory is registered in the configuration', function () {
    set_env('local');
    Config::set('vite.entrypoints', 'scripts');
    App::setBasePath(__DIR__);
    expect(get_vite('with_css.json')->getEntry('entry.ts'))
        ->toEqual('<script type="module" src="http://localhost:3000/scripts/entry.ts"></script>');
});
