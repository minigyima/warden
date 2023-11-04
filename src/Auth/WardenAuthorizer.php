<?php

namespace Minigyima\Warden\Auth;

use Minigyima\Warden\Interfaces\Permission;
use Minigyima\Warden\Services\Warden;

/**
 * The behavioural implementation for Authorization with Warden
 * @package Warden
 */
trait WardenAuthorizer
{
    /**
     * The authorizer function
     * * Throws exceptions if authorization was unsuccessful
     * @param Permission $permission
     * @return boolean
     */
    public function warden_authorize(Permission $permission): bool
    {
        return Warden::use()->authorize($permission, false, false);
    }

    /**
     * Returns the currently loaded Warden instance
     *
     * @return Warden
     */
    protected function warden(): Warden
    {
        return Warden::use();
    }
}
