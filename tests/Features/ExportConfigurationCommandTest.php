<?php

it('exposes the configuration contents to the command line', function () {
    test()->artisan('vite:config')
        ->expectsOutput(\json_encode(\config('vite'), true))
        ->assertExitCode(0);
});
