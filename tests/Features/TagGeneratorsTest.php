<?php

use Innocenzi\Vite\TagGenerators\DefaultTagGenerator;
use Innocenzi\Vite\TagGenerators\TagGenerator;

it('generates a script tag with the specified url', function () {
    expect(app(DefaultTagGenerator::class)->makeScriptTag('https://localhost/build/main.ts'))
        ->toBe('<script type="module" src="https://localhost/build/main.ts"></script>');
});

it('generates a style tag with the specified url', function () {
    expect(app(DefaultTagGenerator::class)->makeStyleTag('https://localhost/build/main.css'))
        ->toBe('<link rel="stylesheet" href="https://localhost/build/main.css" />');
});

it('respects TagGenerator overrides when using the server', function () {
    app()->bind(TagGenerator::class, fn () => new CrossOriginTagGenerator());

    with_dev_server();
    set_base_path_in('');
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

it('respects TagGenerator overrides when not using the server', function () {
    app()->bind(TagGenerator::class, fn () => new CrossOriginTagGenerator());

    set_base_path_in('builds');
    set_env('production');
        
    expect(using_manifest('builds/public/with-css/manifest.json')->getTags())
        ->toContain('<link rel="stylesheet" href="http://localhost/with-css/assets/test.65bd481b.css" crossorigin />')
        ->toContain('<script type="module" src="http://localhost/with-css/assets/test.a2c636dd.js" crossorigin></script>');
});

class CrossOriginTagGenerator implements TagGenerator
{
    public function makeScriptTag(string $url): string
    {
        return sprintf('<script type="module" src="%s" crossorigin></script>', $url);
    }

    public function makeStyleTag(string $url): string
    {
        return sprintf('<link rel="stylesheet" href="%s" crossorigin />', $url);
    }
}
