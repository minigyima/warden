<?php

namespace Minigyima\Warden\Util;

trait HasForcedConnection
{
    public static string|null $forcedConnection = null;

    protected $connection;

    public function getConnectionName(): string|null
    {
        return self::$forcedConnection ?? $this->connection;
    }
}
