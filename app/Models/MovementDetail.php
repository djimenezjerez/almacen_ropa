<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementDetail extends Model
{
    protected $fillable = [
        'movement_id',
        'product_id',
        'stock',
    ];

    public $timestamps = true;

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function storable()
    {
        return $this->morphTo();
    }
}
