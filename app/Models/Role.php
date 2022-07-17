<?php

namespace App\Models;
use Spatie\Permission\Models\Role as SpatieRole;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\PermissionRegistrar;

class Role extends SpatieRole
{
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, config('permission.table_names.model_has_roles'), PermissionRegistrar::$pivotRole, 'store_id');
    }
}
