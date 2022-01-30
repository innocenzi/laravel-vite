<?php

use Innocenzi\Vite\EntrypointsFinder\DefaultEntrypointsFinder;

it('finds anentrypoint by its direct path', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(fixtures_path('/entrypoints/single/main.ts'), []);

    expect($entrypoints)
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getBasename()->toBe('main.ts'));
});

it('finds a single entrypoint in the given directory', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(fixtures_path('/entrypoints/single'), []);

    expect($entrypoints)
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getBasename()->toBe('main.ts'));
});

it('finds multiple entrypoints in the given directory', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(fixtures_path('/entrypoints/multiple'), []);
    
    expect($entrypoints)
        ->toHaveCount(2)
        ->sequence(
            fn ($file) => $file->getBasename()->toBe('main.ts'),
            fn ($file) => $file->getBasename()->toBe('secondary.ts'),
        );
});

it('respects ignore patterns when finding entrypoints', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(
        fixtures_path('/entrypoints/multiple'),
        '/main/'
    );
    
    expect($entrypoints)
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getBasename()->toBe('secondary.ts'));
});

it('finds CSS entrypoints', function () {
    $entrypoints = (new DefaultEntrypointsFinder)->find(fixtures_path('/entrypoints/multiple-with-css'), []);
    
    expect($entrypoints)
        ->toHaveCount(2)
        ->sequence(
            fn ($file) => $file->getBasename()->toBe('main.ts'),
            fn ($file) => $file->getBasename()->toBe('style.css'),
        );
});
