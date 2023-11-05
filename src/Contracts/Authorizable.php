<?php

namespace Minigyima\Warden\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Minigyima\Warden\Models\AuthorizableGroup;

/**
 * The interface used for Authorizable models
 * @package Warden
 */
interface Authorizable
{
    /**
     * Relationship containing the AuthorizableGroups assigned to this user
     *
     * @return MorphToMany
     */
    public function groups(): MorphToMany;

    /**
     * Relationship containing the AuthorizableGroup assigned to this user
     *
     * @return HasOneThrough
     */
    public function role(): HasOneThrough;

    /**
     * Assign a group to this user
     *
     * @param AuthorizableGroup $group
     * @return void
     */
    public function assign(AuthorizableGroup $group): void;

    /**
     * Remove a group from this user
     *
     * @param AuthorizableGroup $group
     * @return void
     */
    public function remove(AuthorizableGroup $group): void;
}
