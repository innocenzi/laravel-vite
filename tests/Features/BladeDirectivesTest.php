<?php

use Illuminate\Support\Facades\Blade;

beforeEach(function () {
    this()->directives = Blade::getCustomDirectives();
});

it('creates the vite, tag, client and react directives', function () {
    expect(this()->directives)
        ->toHaveKeys(['vite', 'tag', 'client', 'react']);
});

it('generates a call to vite_tags() with the default configuration from the @vite directive when there is no parameters', function () {
    expect(this()->directives['vite']())
        ->toBe('<?php echo vite_tags(e("default")); ?>');
});

it('generates a call to vite_tag() with the default configuration from the @vite directive when there is a parameter', function () {
    expect(this()->directives['tag']('"main"'))
        ->toBe('<?php echo vite_tag(e("main"), e("default")); ?>');
});

it('generates a call to vite_tag() with the given configuration from the @tag directive when there is two parameters', function () {
    expect(this()->directives['tag']('"main", "config-name"'))
        ->toBe('<?php echo vite_tag(e("main"), e("config-name")); ?>');
});

it('generates a call to vite_client() from the @client directive with the default configuration when there is no parameter', function () {
    expect(this()->directives['client']())
        ->toBe('<?php echo vite_client(e("default")); ?>');
});

it('generates a call to vite_client() from the @client directive with the specified configuration when there is a parameter', function () {
    expect(this()->directives['client']('"main"'))
        ->toBe('<?php echo vite_client(e("main")); ?>');
});

it('generates a call to vite_react_refresh_runtime() from the @client directive with the default configuration when there is no parameter', function () {
    expect(this()->directives['react']())
        ->toBe('<?php echo vite_react_refresh_runtime(e("default")); ?>');
});

it('generates a call to vite_react_refresh_runtime() from the @client directive with the specified configuration when there is a parameter', function () {
    expect(this()->directives['react']('"main"'))
        ->toBe('<?php echo vite_react_refresh_runtime(e("main")); ?>');
});
