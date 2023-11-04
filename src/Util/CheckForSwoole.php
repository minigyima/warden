<?php

namespace Minigyima\Warden\Util;

use Swoole\Http\Server;
use Illuminate\Support\Facades\App;

class CheckForSwoole
{
    /**
     * Name of the extensions to check for
     * @var string[]
     */
    private static array $extensions = ['openswoole', 'swoole'];

    /**
     * Check loaded extensions
     * @return bool
     */
    public static function check(): bool
    {
        foreach(self::$extensions as $extension)
            if(extension_loaded($extension))
                return true;

        return !App::runningInConsole() && app()->bound(Server::class);
    }
}
