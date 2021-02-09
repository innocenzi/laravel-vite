<?php

namespace Innocenzi\Vite;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Innocenzi\Vite\Vite
 */
class ViteFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-vite';
    }
}
