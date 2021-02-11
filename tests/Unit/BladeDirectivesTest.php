<?php

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    test()->directives = Blade::getCustomDirectives();
});

it('creates directives', function () {
    expect(test()->directives)
        ->toHaveKeys(['vite', 'entry']);
});

it('generates the client script from the directive', function () {
    expect(test()->directives['vite']())
        ->toBe('<?php echo vite_client() ?>');
});

it('generates an script from the directive', function () {
    expect(test()->directives['vite']('resources/ts/main.ts'))
        ->toBe('<?php echo vite_entry(e(resources/ts/main.ts)); ?>');
});
