<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'username',
        'password',
        'access_attempts',
        'active',
        'person_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = true;

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', config('permission.column_names.model_morph_key'), 'role_id')->withPivot('store_id');
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'model_has_roles', 'model_id', 'store_id')->wherePivot('model_type', 'App\Models\User')->withPivot('role_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'remember_role_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'remember_store_id');
    }

    public function movements()
    {
        return $this->hasMany(Movements::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
