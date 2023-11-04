<?php

namespace Minigyima\Warden\Auth;

use Minigyima\Warden\Models\AuthorizableGroup;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * The behavioural implementation for Authorizable models
 * @package Warden
 */
trait AuthorizableBehaviour
{
    /**
     * Relationship containing the AuthorizableGroups assigned to this user
     *
     * @return MorphToMany
     */
    public function groups(): MorphToMany
    {
        return $this->morphToMany(AuthorizableGroup::class, 'authorizable');
    }
}
