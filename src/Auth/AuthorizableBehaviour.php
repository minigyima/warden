<?php

namespace Minigyima\Warden\Auth;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Minigyima\Warden\Models\AuthorizableGroup;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Minigyima\Warden\Errors\InvalidModeException;
use Minigyima\Warden\Models\AuthorizablePivot;

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
     * @throws InvalidModeException (If Warden's role_mode is enabled)
     */
    public function groups(): MorphToMany
    {
        if ((bool) config('warden.role_mode')) {
            throw new InvalidModeException();
        }
        return $this->morphToMany(AuthorizableGroup::class, 'authorizable');
    }

    /**
     * Relationship containing the AuthorizableGroup assigned to this user
     *
     * @return HasOneThrough
     * @throws InvalidModeException (If Warden's role_mode is disabled)
     */
    public function role(): HasOneThrough
    {
        if (!(bool) config('warden.role_mode')) {
            throw new InvalidModeException();
        }
        return $this->hasOneThrough(AuthorizableGroup::class, AuthorizablePivot::class, 'authorizable_id', 'id', 'id', 'authorizable_group_id');
    }

    final protected function detachRoles(): void
    {
        if (!(bool) config('warden.role_mode')) {
            throw new InvalidModeException();
        }
        AuthorizablePivot::where('authorizable_id', $this->id)->where('authorizable_type', static::class)->delete();
    }

    final protected function attachRole(AuthorizableGroup $role): void
    {
        $this->detachRoles();
        $pivot = new AuthorizablePivot();
        $pivot->authorizable_id = $this->id;
        $pivot->authorizable_type = static::class;
        $pivot->authorizable_group_id = $role->id;
        $pivot->save();
    }

    /**
     * Assign a group to this user
     *
     * @param AuthorizableGroup $group
     * @return void
     */
    public function assign(AuthorizableGroup|array $arg): void
    {
        if (is_array($arg) && config('warden.role_mode')) {
            throw new InvalidModeException();
        }

        if ((bool) config('warden.role_mode')) {
            $this->attachRole($arg);
            $this->refresh();
            return;
        }
        $this->groups()->attach($arg);
        $this->refresh();
    }

    /**
     * Remove a group from this user
     *
     * @param AuthorizableGroup $group
     * @return void
     */
    public function remove(AuthorizableGroup|array $arg): void
    {
        if (is_array($arg) && config('warden.role_mode')) {
            throw new InvalidModeException();
        }

        if ((bool) config('warden.role_mode')) {
            $this->detachRoles();
            $this->refresh();
            return;
        }
        $this->groups()->detach($arg);
        $this->refresh();
    }

    /**
     * Remove all groups from this user
     *
     * @return void
     */
    public function removeAll()
    {
        if ((bool) config('warden.role_mode')) {
            $this->detachRoles();
            $this->refresh();
            return;
        }
        $this->groups()->detach();
        $this->refresh();
    }
}
