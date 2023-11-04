<?php

namespace Minigyima\Warden\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The Group model
 * @package Warden
 */
class AuthorizableGroup extends Model
{
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
    public function authorizables(): HasMany
    {
        return $this->hasMany(Authorizable::class);
    }
}
