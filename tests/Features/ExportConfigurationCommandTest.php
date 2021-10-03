<?php

use Illuminate\Support\Facades\File;
use Innocenzi\Vite\Commands\ExportConfigurationCommand;

it('exposes the configuration contents to the command line', function () {
    $output = app(ExportConfigurationCommand::class)->getConfigurationAsJson();

    test()->artisan('vite:config')
        ->expectsOutput($output)
        ->assertExitCode(0);
});

it('writes the configuration contents to the file system if requested', function () {
    File::partialMock()->shouldReceive('put')->withArgs([
        'vite.config.json',
        app(ExportConfigurationCommand::class)->getConfigurationAsJson(),
    ]);

    test()->artisan('vite:config --export=vite.config.json')
        ->expectsOutput('Configuration file written to vite.config.json.')
        ->assertExitCode(0);
});
