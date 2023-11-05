<?php

namespace Minigyima\Warden\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Minigyima\Warden\Util\HasForcedConnection;

/**
 * The pivot model used for linking AuthorizableGroups to Authorizables
 * @package Warden
 */
class AuthorizablePivot extends MorphPivot
{
    use HasForcedConnection;

    public $incrementing = false;
    public $guarded = [];
    protected $table = 'authorizables';
    public $with = ['model'];

    public function model()
    {
        return $this->morphTo();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(AuthorizableGroup::class, 'group_id');
    }
}
