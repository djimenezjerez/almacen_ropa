<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'path',
        'product_name_id',
        'color_id',
        'url',
    ];

    public function name()
    {
        return $this->belongsTo(ProductName::class, 'product_name_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
