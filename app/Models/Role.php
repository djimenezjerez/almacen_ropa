<?php

namespace App\Models;

use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, config('permission.table_names.model_has_roles'), PermissionRegistrar::$pivotRole, 'store_id');
    }
}
