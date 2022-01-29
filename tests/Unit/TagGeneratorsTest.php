<?php

use Innocenzi\Vite\TagGenerators\DefaultTagGenerator;

it('generates a script tag with the specified url', function () {
    expect(app(DefaultTagGenerator::class)->makeScriptTag('https://localhost/build/main.ts'))
        ->toBe('<script type="module" src="https://localhost/build/main.ts"></script>');
});

it('generates a style tag with the specified url', function () {
    expect(app(DefaultTagGenerator::class)->makeStyleTag('https://localhost/build/main.css'))
        ->toBe('<link rel="stylesheet" href="https://localhost/build/main.css" />');
});
