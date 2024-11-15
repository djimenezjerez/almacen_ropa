<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    protected $fillable = [
        'client_id',
        'total',
        'state',
        'attachment_file',
        'attachment_type',
        'comment',
    ];

    protected $casts = [
        'total' => 'float',
    ];

    public function products()
    {
        return $this->hasMany(ShoppingCartProduct::class);
    }
}
