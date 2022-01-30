<?php

use Innocenzi\Vite\EntrypointsFinder\DefaultEntrypointsFinder;

it('finds anentrypoint by its direct path', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(__DIR__ . '/entrypoints/single/main.ts', []);

    expect($entrypoints)
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getBasename()->toBe('main.ts'));
});

it('finds a single entrypoint in the given directory', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(__DIR__ . '/entrypoints/single', []);

    expect($entrypoints)
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getBasename()->toBe('main.ts'));
});

it('finds multiple entrypoints in the given directory', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(__DIR__ . '/entrypoints/multiple', []);
    
    expect($entrypoints)
        ->toHaveCount(2)
        ->sequence(
            fn ($file) => $file->getBasename()->toBe('main.ts'),
            fn ($file) => $file->getBasename()->toBe('secondary.ts'),
        );
});

it('respects ignore patterns when finding entrypoints', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(
        __DIR__ . '/entrypoints/multiple',
        '/main/'
    );
    
    expect($entrypoints)
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getBasename()->toBe('secondary.ts'));
});

it('finds CSS entrypoints', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(__DIR__ . '/entrypoints/multiple-with-css', []);
    
    expect($entrypoints)
        ->toHaveCount(2)
        ->sequence(
            fn ($file) => $file->getBasename()->toBe('main.ts'),
            fn ($file) => $file->getBasename()->toBe('style.css'),
        );
});
