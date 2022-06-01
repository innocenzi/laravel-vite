<?php

use Innocenzi\Vite\Chunk;
use Innocenzi\Vite\TagGenerators\CallbackTagGenerator;
use Innocenzi\Vite\TagGenerators\DefaultTagGenerator;
use Innocenzi\Vite\TagGenerators\TagGenerator;
use Innocenzi\Vite\Vite;

it('uses the callback tag generator by default', function () {
    expect(app(TagGenerator::class))->toBeInstanceOf(CallbackTagGenerator::class);
});

it('generates a script tag with the specified url', function () {
    expect(app(DefaultTagGenerator::class)->makeScriptTag('https://localhost/build/main.ts'))
        ->toBe('<script type="module" src="https://localhost/build/main.ts"></script>');
});

it('generates a style tag with the specified url', function () {
    expect(app(DefaultTagGenerator::class)->makeStyleTag('https://localhost/build/main.css'))
        ->toBe('<link rel="stylesheet" href="https://localhost/build/main.css" />');
});

it('respects TagGenerator overrides in development', function () {
    app()->bind(TagGenerator::class, fn () => new CrossOriginTagGenerator());

    with_dev_server();
    set_fixtures_path('');
    set_env('local');
    set_vite_config('default', [
        'entrypoints' => [
            'paths' => 'entrypoints/multiple-with-css',
        ],
    ]);
    
    expect(vite()->getTags())
        ->toContain('<script type="module" src="http://localhost:5173/@vite/client" crossorigin></script>')
        ->toContain('<script type="module" src="http://localhost:5173/entrypoints/multiple-with-css/main.ts" crossorigin></script>')
        ->toContain('<link rel="stylesheet" href="http://localhost:5173/entrypoints/multiple-with-css/style.css" crossorigin />');
});

it('respects TagGenerator callback overrides in development', function () {
    Vite::makeScriptTagsUsing(function (string $url): string {
        return sprintf('<script type="module" src="%s" crossorigin="anonymous"></script>', $url);
    });

    Vite::makeStyleTagsUsing(function (string $url): string {
        return sprintf('<link rel="stylesheet" href="%s" crossorigin="anonymous" />', $url);
    });

    with_dev_server();
    set_fixtures_path('');
    set_env('local');
    set_vite_config('default', [
        'entrypoints' => [
            'paths' => 'entrypoints/multiple-with-css',
        ],
    ]);
    
    expect(vite()->getTags())
        ->toContain('<script type="module" src="http://localhost:5173/@vite/client" crossorigin="anonymous"></script>')
        ->toContain('<script type="module" src="http://localhost:5173/entrypoints/multiple-with-css/main.ts" crossorigin="anonymous"></script>')
        ->toContain('<link rel="stylesheet" href="http://localhost:5173/entrypoints/multiple-with-css/style.css" crossorigin="anonymous" />');

    Vite::makeScriptTagsUsing();
    Vite::makeStyleTagsUsing();
});

it('respects TagGenerator overrides in production', function () {
    app()->bind(TagGenerator::class, fn () => new CrossOriginTagGenerator());

    set_fixtures_path('builds');
    set_env('production');
        
    expect(using_manifest('builds/public/with-css/manifest.json')->getTags())
        ->toContain('<link rel="stylesheet" href="http://localhost/with-css/assets/test.65bd481b.css" crossorigin />')
        ->toContain('<script type="module" src="http://localhost/with-css/assets/test.a2c636dd.js" crossorigin></script>');
});

it('uses integrity attributes when the chunk contains them', function () {
    set_fixtures_path('builds');
    set_env('production');
        
    expect(using_manifest('builds/public/with-integrity/manifest.json')->getTags())
        ->toContain('<link rel="stylesheet" href="http://localhost/with-integrity/assets/main.65bd481b.css" />')
        ->toContain('<script type="module" src="http://localhost/with-integrity/assets/main.a2c636dd.js" integrity="sha384-2A5vUNf7cFDCWm6RTDPAnr/wmGjkQhXz4EP5keVPYX4OnI2Ws1iXgTQ70CTmC1Ux" crossorigin="anonymous"></script>')
        ->toContain('<link rel="stylesheet" href="http://localhost/with-integrity/assets/style.6b73f2d0.css" integrity="sha384-HmlJ0WJXVNLaqWQYVRhZgsW1JBN4XBjm3j+j38nagNFimvBxi8nZ1FUysAc9JJvL" crossorigin="anonymous" />');
});

class CrossOriginTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url, Chunk $chunk = null): string
    {
        return sprintf('<script type="module" src="%s" crossorigin></script>', $url);
    }

    public function makeStyleTag(string $url, Chunk $chunk = null): string
    {
        return sprintf('<link rel="stylesheet" href="%s" crossorigin />', $url);
    }
}
