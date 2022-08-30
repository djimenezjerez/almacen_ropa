<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = [
        'name',
        'size_type_id',
        'numeric',
    ];

    protected $casts = [
        'numeric' => 'boolean',
    ];

    public $timestamps = false;

    public function size_type()
    {
        return $this->belongsTo(SizeType::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
