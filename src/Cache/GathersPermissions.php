<?php

namespace Minigyima\Warden\Cache;

use Minigyima\Warden\Contracts\Authorizable;

/**
 * Gathers permissions for an Authorizable
 * @package Warden
 */
trait GathersPermissions
{
    /**
     * Gathers all permissions for an Authorizable
     *
     * @param Authorizable $authorizable
     * @return array<int, permission-string>
     */
    final protected function gatherPermissions(Authorizable $authorizable): array
    {
        $role_mode = (bool) config('warden.role_mode');

        if (!$role_mode) {
            $group_permissions = $authorizable->groups()->get()->pluck('permissions')->toArray();
            return array_merge(...$group_permissions);
        } else {
            $role = $authorizable->role()->first();
            if ($role === null) {
                return [];
            }
            return (array) $role->permissions;
        }
    }
}
