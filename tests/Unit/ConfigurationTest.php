<?php

use Innocenzi\Vite\Exceptions\NoSuchConfigurationException;

it('uses the default configuration when not specifying one', function () {
    expect(vite()->getClientScriptTag())
        ->toEqual('<script type="module" src="http://localhost:3000/@vite/client"></script>');
});

it('throws when accessing a configuration that does not exist', function () {
    vite('unknown-configuration')->getClientScriptTag();
})->throws(NoSuchConfigurationException::class);

it('uses the right configuration when specifying it', function () {
    set_vite_config('custom', [
        'dev_server' => [
            'url' => 'http://localhost:3001',
        ],
    ]);

    expect(vite('custom')->getConfig('dev_server.url'))->toBe('http://localhost:3001');
});
