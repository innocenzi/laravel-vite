<?php

use Illuminate\Support\Facades\File;

it('generates a tsconfig.json file with the configured aliases', function () {
    sandbox(function () {
        $tsconfigPath = base_path('tsconfig.json');
        config()->set('vite.aliases', [
            '@' => 'resources',
            '@scripts' => 'resources/scripts',
        ]);

        expect(File::exists($tsconfigPath))->toBeFalse();
        this()->artisan('vite:tsconfig')->assertExitCode(0);
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

it('preserves indentation of the existing tsconfig.json', function () {
    sandbox(function () {
        $tsconfigPath = base_path('tsconfig.json');
        config()->set('vite.aliases', [
            '@' => 'resources',
            '@scripts' => 'resources/scripts',
        ]);

        File::put($tsconfigPath, <<<JSON
        {
          "compilerOptions": {
            "baseUrl": "."
          }
        }
        JSON);

        this()->artisan('vite:tsconfig')->assertExitCode(0);
        
        expect(File::exists($tsconfigPath))->toBeTrue();
        expect(explode('"', explode("\n", File::get($tsconfigPath))[1])[0])->toBe('  ');
    }, preserve: true);
});

it('throws an error if the tsconfig is malformatted', function () {
    sandbox(function () {
        $tsconfigPath = base_path('tsconfig.json');

        // This JSON has a trailing comma
        File::put($tsconfigPath, <<<JSON
        {
            "compilerOptions": {
                "baseUrl": ".",
            }
        }
        JSON);

        this()->artisan('vite:tsconfig');
    });
})->throws(RuntimeException::class);

it('asks for confirmation before running in production', function () {
    sandbox(function () {
        set_env('production');
        this()->artisan('vite:tsconfig')
            ->expectsConfirmation(
                question: 'Are you sure you want to run this command?',
                answer: 'yes'
            )
            ->assertExitCode(0);

        expect(File::exists(base_path('tsconfig.json')))->toBeTrue();
    });
});

it('does not ask for confirmation before running in production if --force is given', function () {
    sandbox(function () {
        set_env('production');
        this()->artisan('vite:tsconfig', ['--force' => true])->assertExitCode(0);
        expect(File::exists(base_path('tsconfig.json')))->toBeTrue();
    });
});
