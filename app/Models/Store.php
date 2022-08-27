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
    ];

    protected $casts = [
        'active' => 'boolean',
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

    public function origin_transfers()
    {
        return $this->hasMany(StockTransfer::class, 'origin_warehouse_id', 'id');
    }

    public function destiny_transfers()
    {
        return $this->hasMany(StockTransfer::class, 'destiny_warehouse_id', 'id');
    }
}
