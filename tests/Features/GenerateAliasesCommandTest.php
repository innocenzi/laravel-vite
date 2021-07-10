<?php

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

it('generates a tsconfig.json file with the updated aliases', function () {
    sandbox(function () {
        $tsconfigPath = base_path('tsconfig.json');
        Config::set('vite.aliases', [
            '@' => 'resources',
            '@scripts' => 'resources/scripts',
        ]);

        expect(File::exists($tsconfigPath))->toBeFalse();
        test()->artisan('vite:aliases')->assertExitCode(0);
        expect(File::exists($tsconfigPath))->toBeTrue();

        expect(json_decode(File::get($tsconfigPath), true)['compilerOptions'])
            ->toMatchArray([
                'baseUrl' => '.',
                'paths' => [
                    '@/*' => ['resources/*'],
                    '@scripts/*' => ['resources/scripts/*'],
                ],
            ]);
    });
});

it('does not create a tsconfig.json file if aliases are disabled', function () {
    sandbox(function () {
        $tsconfigPath = base_path('tsconfig.json');
        Config::set('vite.aliases', false);

        expect(File::exists($tsconfigPath))->toBeFalse();
        test()->artisan('vite:aliases')->assertExitCode(0);
        expect(File::exists($tsconfigPath))->toBeFalse();
    });
});

it('merges path aliases with existing ones', function () {
    sandbox(function () {
        $tsconfigPath = base_path('tsconfig.json');

        Config::set('vite.aliases', [
            '@' => 'resources',
            '@scripts' => 'resources/scripts',
        ]);

        File::put($tsconfigPath, json_encode([
            'compilerOptions' => [
                'paths' => ['@test/*' => ['test/*']],
            ],
        ]));

        test()->artisan('vite:aliases')->assertExitCode(0);
        expect(File::exists($tsconfigPath))->toBeTrue();

        expect(json_decode(File::get($tsconfigPath), true)['compilerOptions'])
            ->toMatchArray([
                'baseUrl' => '.',
                'paths' => [
                    '@/*' => ['resources/*'],
                    '@scripts/*' => ['resources/scripts/*'],
                    '@test/*' => ['test/*'],
                ],
            ]);
    });
});
