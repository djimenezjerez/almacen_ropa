<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductName extends Model
{
    protected $fillable = [
        'name',
        'category_id',
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
