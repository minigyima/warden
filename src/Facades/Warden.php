<?php

namespace Minigyima\Warden\Facades;

use Illuminate\Support\Facades\Facade;
use Minigyima\Warden\Services\Warden as ServicesWarden;

class Warden extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return ServicesWarden::class;
    }
}
