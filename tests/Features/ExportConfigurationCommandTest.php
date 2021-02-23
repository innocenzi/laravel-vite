<?php

use Innocenzi\Vite\Commands\ExportConfigurationCommand;

it('exposes the configuration contents to the command line', function () {
    $output = app(ExportConfigurationCommand::class)->getConfigurationAsJson();

    test()->artisan('vite:config')
        ->expectsOutput($output)
        ->assertExitCode(0);
});
