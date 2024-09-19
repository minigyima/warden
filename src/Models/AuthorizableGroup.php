<?php

namespace Minigyima\Warden\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Minigyima\Warden\Facades\Warden;
use Minigyima\Warden\Interfaces\Permission;
use Minigyima\Warden\Util\HasForcedConnection;

/**
 * The Group (or Role) model
 * @package Warden
 */
class AuthorizableGroup extends Model
{
    use HasForcedConnection;

    /**
     * Hidden attributes, the pivot model hidden by default
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Casts, the permissions are cast to an ArrayObject, written to the database as JSON
     *
     * @var array
     */
    protected $casts = [
        'permissions' => AsArrayObject::class,
    ];

    /**
     * Fillable attributes
     *
     * @var array
     */
    protected $fillable = ['name', 'permissions'];

    /**
     * The name of the table
     *
     * @var string
     */
    protected $table = 'authorizable_groups';

    /**
     * Generate groups via a factory
     */
    use HasFactory;

    /**
     * The relation which returns all the pivot models
     *
     * @return HasMany
     */
    public function authorizablePivots(): HasMany
    {
        return $this->hasMany(AuthorizablePivot::class);
    }

    /**
     * The relation which returns all the models which are assigned to this group
     *
     * @return Collection<int, Authorizable>
     */
    public function authorizables(): Collection
    {
        return $this->authorizablePivots()->get()->map(function ($pivot) {
            return $pivot->authorizable;
        });
    }

    /**
     * Invalidates all Authorizables associated with this group
     *
     * @return void
     */
    public function invalidateAllAuthorizables(): void
    {
        $ids = $this->authorizables()->pluck('id')->toArray();
        Warden::invalidate($ids);
    }

    /**
     * Grants a permission or permissions to this group
     * @param Permission|array<int, Permission> $arg
     * @return void
     * @throws \Exception
     */
    public function grant(Permission|array $arg): void
    {
        $permissions = [...$this->permissions];
        $permissions = array_merge($permissions, array_map(function ($p) {
            return $p->permissionString();
        }, is_array($arg) ? $arg : [$arg]));
        $this->permissions = $permissions;
        $this->save();
        $this->invalidateAllAuthorizables();
    }

    /**
     * Revokes a permission or permissions from this group
     * @param Permission|array<int, Permission> $arg
     * @return void
     * @throws \Exception
     */
    public function revoke(Permission|array $arg): void
    {
        $permissions = [...$this->permissions];
        $permissions = array_diff($permissions, array_map(function ($p) {
            return $p->permissionString();
        }, is_array($arg) ? $arg : [$arg]));
        $this->permissions = $permissions;
        $this->save();
        $this->invalidateAllAuthorizables();
    }
}
