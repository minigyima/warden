<?php

namespace Minigyima\Warden\Facades;

use Illuminate\Support\Facades\Facade;

class Warden extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Warden::class;
    }
}
