<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementDetail extends Model
{
    protected $fillable = [
        'movement_id',
        'product_id',
        'discount',
        'stock',
        'store_id',
    ];

    public $timestamps = true;

    protected $casts = [
        'discount' => 'float',
    ];

    public function movement()
    {
        return $this->belongsTo(Movement::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
