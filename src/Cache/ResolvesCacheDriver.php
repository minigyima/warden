<?php

namespace Minigyima\Warden\Cache;

use Minigyima\Warden\Interfaces\ColdCacheDriver;
use Minigyima\Warden\Interfaces\WarmCacheDriver;

/**
 * Trait used for resolving the currently configured CacheDrivers
 * @package Warden
 */
trait ResolvesCacheDriver
{
    /**
     * Returns a new instance of the configured warm cache driver
     *
     * @return WarmCacheDriver
     */
    private static function newWarmCacheDriver(): WarmCacheDriver
    {
        $driver = config('warden.warm_cache_driver');
        return new $driver();
    }

    /**
     * Returns a new instance of the configured warm cache driver
     *
     * @return ColdCacheDriver
     */
    private static function newColdCacheDriver(): ColdCacheDriver
    {
        $driver = config('warden.cold_cache_driver');
        return new $driver();
    }
}
