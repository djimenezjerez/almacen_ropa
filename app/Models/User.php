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

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'model_has_roles', 'model_id', 'model_id')->wherePivot('model_type', 'App\Models\User');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
