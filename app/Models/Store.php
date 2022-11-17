<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active',
        'person_id',
        'warehouse',
    ];

    protected $casts = [
        'active' => 'boolean',
        'warehouse' => 'boolean',
    ];

    public $timestamps = true;

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'store_id', 'model_id')->wherePivot('model_type', 'App\Models\User');
    }

    public function movements_from()
    {
        return $this->hasMany(Store::class, 'from_store_id');
    }

    public function movements_to()
    {
        return $this->hasMany(Store::class, 'to_store_id');
    }

    public function movement_details()
    {
        return $this->hasMany(MovementDetail::class);
    }
}
