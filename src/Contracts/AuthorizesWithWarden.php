<?php

namespace Minigyima\Warden\Contracts;

use Minigyima\Warden\Interfaces\Permission;

/**
 * The contract used for Authorization via Warden
 * @package Warden
 */
interface AuthorizesWithWarden
{
    /**
     * The authorizer function
     * * Throws exceptions if authorization was unsuccessful
     * @param Permission $permission
     * @return boolean
     */
    public function warden_authorize(Permission $permission): bool;
}
