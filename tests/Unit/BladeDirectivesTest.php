<?php

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    test()->directives = Blade::getCustomDirectives();
});

it('creates directives', function () {
    expect(test()->directives)
        ->toHaveKeys(['vite', 'client']);
});

it('generates a call to vite_tags() from the @vite directive when there is no parameters', function () {
    expect(test()->directives['vite']())
        ->toBe('<?php echo vite_tags() ?>');
});

it('generates a call to vite_entry() from the @vite directive when there is a parameter', function () {
    expect(test()->directives['vite']('resources/ts/main.ts'))
        ->toBe('<?php echo vite_entry(e(resources/ts/main.ts)); ?>');
});

it('generates a call to vite_client() from the @client directive', function () {
    expect(test()->directives['client']())
        ->toBe('<?php echo vite_client(); ?>');
});
