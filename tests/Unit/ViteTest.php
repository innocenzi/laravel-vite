<?php

use Innocenzi\Vite\Vite;

it('forwards calls to the default configuration', function () {
    expect(app(Vite::class)->getConfig())->toBeArray();
});
