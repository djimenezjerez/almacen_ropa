<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SizeType extends Model
{
    protected $fillable = [
        'name',
        'order',
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
