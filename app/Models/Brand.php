<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $hidden = [
        'laravel_through_key',
    ];

    protected $fillable = [
        'name',
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
}
