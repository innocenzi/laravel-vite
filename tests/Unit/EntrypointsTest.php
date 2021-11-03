<?php

use Innocenzi\Vite\Vite;

it('finds entrypoints in an absolute directory', function () {
    config()->set('vite.entrypoints', [
        __DIR__ . '/scripts',
    ]);

    expect(app(Vite::class)->findEntrypoints())
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getFileName()->toBe('entry.ts'));
});

it('finds entrypoints in a relative directory', function () {
    app()->setBasePath(__DIR__);
    config()->set('vite.entrypoints', [
        '/scripts',
    ]);

    expect(app(Vite::class)->findEntrypoints())
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getFileName()->toBe('entry.ts'));
});

it('filters entrypoints in an absolute directory', function () {
    config()->set('vite.entrypoints', [
        __DIR__ . '/scripts-dts',
    ]);

    config()->set('vite.ignore_patterns', [
        '/\\.d\\.ts$/',
    ]);

    expect(app(Vite::class)->findEntrypoints())
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getFileName()->toBe('entry.ts'));
});

it('finds a specific absolute entrypoint', function () {
    config()->set('vite.entrypoints', [
        __DIR__ . '/scripts/entry.ts',
    ]);

    expect(app(Vite::class)->findEntrypoints())
        ->toHaveCount(1)
        ->sequence(fn ($file) => $file->getFileName()->toBe('entry.ts'));
});

it('ignores entrypoints when the config is set to false', function () {
    config(['vite.entrypoints' => false]);

    expect(app(Vite::class)->findEntrypoints())
        ->toHaveCount(0);
});
