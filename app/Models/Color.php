<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $fillable = [
        'name',
        'hex',
    ];
    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function image(ProductName $productName)
    {
        return $this->images()->where('product_name_id', $productName->id)->first();
    }
}
