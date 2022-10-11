<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function product_names()
    {
        return $this->hasMany(ProductName::class);
    }
}
