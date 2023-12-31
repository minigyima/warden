<?php

namespace Minigyima\Warden\Cache\Models;

use Minigyima\Warden\Cache\GathersPermissions;
use Minigyima\Warden\Contracts\Authorizable;
use Minigyima\Warden\Interfaces\ColdCacheDriver;
use Minigyima\Warden\Interfaces\WarmCacheDriver;
use Minigyima\Warden\Interfaces\Permission;

/**
 * The entry associated with an Authorizable in the cache
 * @package Warden
 */
class CacheEntry
{
    use GathersPermissions;
    /**
     * Does the Authorizable have SuperUser permissions
     *
     * @var boolean
     */
    public readonly bool $is_authorizable_super;

    /**
     * The Authorizable associated with this entry
     *
     * @var Authorizable
     */
    public readonly Authorizable $authorizable;

    /**
     * The currently loaded warm cache driver
     *
     * @var WarmCacheDriver
     */
    private readonly WarmCacheDriver $warm_cache;

    /**
     * The currently loaded cold cache driver
     *
     * @var ColdCacheDriver
     */
    private readonly ColdCacheDriver $cold_cache;

    /**
     * Every Permission of the associated Authorizable
     *
     * @var array
     */
    private array $cached_permissions;

    /**
     * The already determined Permissions
     *
     * @var array
     */
    private array $all_permissions;

    /**
     * Resumes an entry from JSON
     *
     * @param string $serialized_data
     * @return void
     */
    private function unserialize(string $serialized_data): void
    {
        $saved = json_decode($serialized_data, true) ?? [
            'cached_permissions' => [],
            'all_permissions' => $this->gatherPermissions($this->authorizable),
        ];
        $this->cached_permissions = $saved['cached_permissions'];
        $this->all_permissions = $saved['all_permissions'];
    }

    /**
     * Suspends an entry to JSON
     *
     * @return string
     */
    public function serialize(): string
    {
        return json_encode(
            (object) [
                'cached_permissions' => $this->cached_permissions,
                'all_permissions' => $this->all_permissions,
            ]
        );
    }

    /**
     * Determines whether the Authorizable is a SuperUser or not
     *
     * @return boolean
     */
    private function setSuper(): bool
    {
        $key = $this->cold_cache->authorizableSuperKey();
        return in_array($this->authorizable->{$key}, $this->cold_cache->superUserArray());
    }

    /**
     * Checks if the entry is in the invalidation Queue
     *
     * If it is, the entry is invalidated and refreshed
     *
     * @return boolean
     */
    private function checkInvalidate(): bool
    {
        $key = $this->cold_cache->authorizableIdentifierKey();
        if (in_array($this->authorizable->{$key}, $this->warm_cache->invalidatedKeys())) {
            $this->warm_cache->invalidateCache($this);
            $this->warm_cache->invalidatedSuccess($this);
            return true;
        }
        return false;
    }

    /**
     * Resets the cached permissions to a default state
     *
     * @return null
     */
    private function resetCache(): null
    {
        $this->all_permissions = $this->gatherPermissions($this->authorizable);
        $this->cached_permissions = [];
        return null;
    }

    /**
     * Constructor, Bootstraps the Entry, checks for invalidation, and loads the permissions if necessary.
     *
     * @param Authorizable $authorizable
     * @param WarmCacheDriver $warm_cache
     * @param string $serialized_data
     */
    public function __construct(
        Authorizable $authorizable,
        WarmCacheDriver $warm_cache,
        ColdCacheDriver $cold_cache,
        string $serialized_data
    ) {
        $this->authorizable = $authorizable;
        $this->warm_cache = $warm_cache;
        $this->cold_cache = $cold_cache;
        $this->is_authorizable_super = $this->setSuper();
        if ($this->checkInvalidate()) {
            $this->resetCache();
        } else {
            $this->unserialize($serialized_data);
        }
    }

    /**
     * Returns all of the permissions of the Authorizable associated with the entry
     *
     * @return array
     */
    public function allPermissions(): array
    {
        return $this->all_permissions;
    }

    /**
     * Tries to determine whether the Authorizable has a given permission or
     * not from the list of already determined permissions.
     *
     * @param string $permissionString
     * @return boolean|null Null, if the Permission has not yet been determined
     */
    private function tryCache(string $permissionString): bool|null
    {
        if ($this->checkInvalidate()) {
            return $this->resetCache();
        }

        if (array_key_exists($permissionString, $this->cached_permissions)) {
            return $this->cached_permissions[$permissionString];
        }

        return null;
    }

    /**
     * Determines if the user has a permission or not
     *
     * @param Permission $permission
     * @return boolean
     */
    public function get(Permission $permission): bool
    {
        $permissionString = $this->cold_cache->resolvePermission($permission);
        return $this->tryCache($permissionString) ??
            ($this->cached_permissions[$permissionString] = (bool) in_array($permissionString, $this->all_permissions));
    }

    /**
     * Suspends itself to JSON, and writes to the cache on shutdown
     */
    public function __destruct()
    {
        $this->warm_cache->setCache($this);
    }
}
