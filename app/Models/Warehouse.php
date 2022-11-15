<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active',
        'person_id',
        'user_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_person()
    {
        return $this->belongsTo(Person::class, User::class);
    }

    public function origin_transfers()
    {
        return $this->hasMany(StockTransfer::class, 'origin_storable');
    }

    public function destiny_transfers()
    {
        return $this->hasMany(StockTransfer::class, 'destiny_storable');
    }

    public function movements_from()
    {
        return $this->morphMany(Movement::class, 'fromable');
    }

    public function movements_to()
    {
        return $this->morphMany(Movement::class, 'toable');
    }

    public function movements()
    {
        return $this->morphMany(MovementDetail::class, 'storable');
    }
}
