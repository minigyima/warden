<?php

namespace Minigyima\Warden\Cache;

use Illuminate\Support\Facades\Log;
use Minigyima\Warden\Errors\InvalidPermissionException;
use Minigyima\Warden\Errors\PermissionNotResolvedException;
use Minigyima\Warden\Interfaces\ColdCacheDriver;
use Minigyima\Warden\Interfaces\Permission;

/**
 * The default cold caching implementation used by Warden
 * * Provides the Access Control List
 * @package Warden
 */
class ColdCacheManager implements ColdCacheDriver
{
    /**
     * Resolved Permissions and their PermissionStrings
     *
     * @var array
     */
    private array $permission_resolutions;

    /**
     * The same as 'permission_resolutions', but the key-value pairs are flipped
     *
     * @var array
     */
    private array $permission_resolutions_flipped;

    /**
     * The cached permission scopes, and all of their permissions
     *
     * @var array
     */
    private array $scope_cache;

    /**
     * The configured identifier field on Authorizable objects
     *
     * @var string
     */
    private string $authorizable_identifier_key;

    /**
     * The prefix applied to all Authorizable cache entries
     *
     * @var string
     */
    private string $cache_prefix;

    /**
     * The configured superuser field on Authorizable objects
     *
     * @var string
     */
    private string $authorizable_super_key;

    /**
     * The configured array of valid superuser 'super_key' values
     *
     * @var array
     */
    private array $super_user_array;

    /**
     * Loads the 'Cold' cache information containing the Permission map, and resolutions
     *
     * @return void
     */
    private function loadColdCache(): void
    {
        if (!file_exists(config('warden.cache_path'))) {
            Log::warning('Warden / ColdCacheManager: Cache unavailable. Rebuilding...');
            ColdCacheBuilder::rebuild();
            Log::info('Warden / ColdCacheManager: Cache rebuild successfully! Booting...');
        }

        $cached = require config('warden.cache_path');
        $this->permission_resolutions = $cached['permission_resolutions'];
        $this->permission_resolutions_flipped = $cached['permission_resolutions_flipped'];
        $this->scope_cache = $cached['scope_cache'];
    }

    /**
     * Caches the configuration on startup
     *
     * @return void
     */
    private function loadConfig(): void
    {
        $this->authorizable_identifier_key = config('warden.authorizable_key');
        $this->cache_prefix = config('warden.cache_prefix');
        $this->authorizable_super_key = config('warden.authorizable_super_key');
        $this->super_user_array = config('warden.superusers');
    }

    /**
     * Constructor. Caches the Warden's configuration and loads the ACL
     */
    public function __construct()
    {
        $this->loadColdCache();
        $this->loadConfig();
    }

    /**
     * Returns the 'Cold' Scope cache (and all permissions in each scope)
     *
     * @return array
     */
    public function scopeCache(): array
    {
        return $this->scope_cache;
    }

    /**
     * Returns the PermissionString-Class resolutions for all loaded Permission objects
     *
     * @return array
     */
    public function permissionResolutions(): array
    {
        return $this->permission_resolutions;
    }

    /**
     * Returns the Class-PermissionString resolutions for all loaded Permission objects
     *
     * @return array
     */
    public function permissionResolutionsFlipped(): array
    {
        return $this->permission_resolutions_flipped;
    }

    /**
     * Returns the cached superuser field on Authorizable objects
     *
     * @return string
     */
    public function authorizableSuperKey(): string
    {
        return $this->authorizable_super_key;
    }

    /**
     * Returns the cached identifier field on Authorizable objects
     *
     * @return string
     */
    public function authorizableIdentifierKey(): string
    {
        return $this->authorizable_identifier_key;
    }

    /**
     * Returns the cached array of superuser 'super_key' values
     *
     * @return array
     */
    public function superUserArray(): array
    {
        return $this->super_user_array;
    }

    /**
     * Returns the cached prefix applied to all keys in a given cache store
     *
     * @return string
     */
    public function cache_prefix(): string
    {
        return $this->cache_prefix;
    }

    /**
     * Resolves a given Permission's PermissionString
     *
     * @param Permission $permission
     * @return permission-string
     */
    public function resolvePermission(Permission $permission): string
    {
        if (array_key_exists($permission::class, $this->permission_resolutions_flipped)) {
            return $this->permission_resolutions_flipped[$permission::class];
        }
        throw new PermissionNotResolvedException(
            'Permission ' . $permission::class . ' could not be resolved from the Cache. Please rebuild'
        );
    }

    /**
     * Resolves a Permission from a permissionString
     *
     * @param permission-string $permissionString
     * @return Permission
     */
    public function resolvePermissionFromString(string $permissionString): Permission
    {

        if (!array_key_exists($permissionString, $this->permission_resolutions)) {
            throw new InvalidPermissionException("Invalid Permission $permissionString");
        }

        $permission = new ($this->permission_resolutions[$permissionString]);
        if ($permission instanceof Permission) {
            return $permission;
        }
        throw new InvalidPermissionException("Invalid Permission $permissionString");
    }
}
