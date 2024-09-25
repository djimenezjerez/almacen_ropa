<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCartProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'shopping_carts_id',
        'product_id',
        'quantity',
        'sell_price',
        'subtotal',
    ];

    protected $casts = [
        'sell_price' => 'float',
        'subtotal' => 'float',
    ];

    public function shopping_cart()
    {
        return $this->belongsTo(ShoppingCart::class);
    }

    public function product()
    {
        return $this->belongsTo(ShoppingCart::class);
    }
}
