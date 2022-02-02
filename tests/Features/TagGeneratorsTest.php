<?php

use Illuminate\Support\Str;
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

it('generates a script tag with the specified attributes', function () {
    $url = 'https://localhost/build/main.ts';
    $attributes = [
        'integrity' => $integrity = Str::random(),
        'data-empty' => '',
        'data-truthy' => true,
        'data-falsy' => false,
        'data-nullish' => null,
    ];
    
    expect(app(DefaultTagGenerator::class)->makeScriptTag($url, $attributes))
        ->toBe(sprintf('<script type="module" src="%s" integrity="%s" data-empty data-truthy="true" data-falsy="false"></script>', $url, $integrity));
});

it('generates a style tag with the specified attributes', function () {
    $url = 'https://localhost/build/main.css';
    $attributes = [
        'integrity' => $integrity = Str::random(),
        'crossorigin' => 'anonymous',
        'data-empty' => '',
        'data-truthy' => true,
        'data-falsy' => false,
        'data-nullish' => null,
    ];
    
    expect(app(DefaultTagGenerator::class)->makeStyleTag($url, $attributes))
        ->toBe(sprintf('<link rel="stylesheet" href="%s" integrity="%s" crossorigin="anonymous" data-empty data-truthy="true" data-falsy="false" />', $url, $integrity));
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
        ->toContain('<script type="module" src="http://localhost:3000/@vite/client" crossorigin></script>')
        ->toContain('<script type="module" src="http://localhost:3000/entrypoints/multiple-with-css/main.ts" crossorigin></script>')
        ->toContain('<link rel="stylesheet" href="http://localhost:3000/entrypoints/multiple-with-css/style.css" crossorigin />');
});

it('respects TagGenerator callback overrides in development', function () {
    Vite::makeScriptTagsUsing(function (string $url, array $attributes = []): string {
        return sprintf('<script type="module" src="%s" data-test="passes"></script>', $url);
    });

    Vite::makeStyleTagsUsing(function (string $url, array $attributes = []): string {
        return sprintf('<link rel="stylesheet" href="%s" data-test="passes" />', $url);
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
        ->toContain('<script type="module" src="http://localhost:3000/@vite/client" data-test="passes"></script>')
        ->toContain('<script type="module" src="http://localhost:3000/entrypoints/multiple-with-css/main.ts" data-test="passes"></script>')
        ->toContain('<link rel="stylesheet" href="http://localhost:3000/entrypoints/multiple-with-css/style.css" data-test="passes" />');

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

it('uses integrity attributes by default in production', function () {
    set_fixtures_path('builds');
    set_env('production');
        
    expect(using_manifest('builds/public/with-integrity/manifest.json')->getTags())
        ->toContain('<link rel="stylesheet" href="http://localhost/with-integrity/assets/test.65bd481b.css" />')
        ->toContain('<script type="module" src="http://localhost/with-integrity/assets/test.a2c636dd.js" integrity="sha384-zg8Jm3p3VNdiPBVvWkVaQGn1pi/3TJ7fRRFsdaoyR74qhPmB7d3Sl4cA38EAEVkf"></script>');
});

class CrossOriginTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url, array $attributes = []): string
    {
        return sprintf('<script type="module" src="%s" crossorigin></script>', $url);
    }

    public function makeStyleTag(string $url, array $attributes = []): string
    {
        return sprintf('<link rel="stylesheet" href="%s" crossorigin />', $url);
    }
}
