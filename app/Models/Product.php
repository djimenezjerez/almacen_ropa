<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'active',
        'category_id',
        'brand_id',
        'size_id',
        'size_type_id',
        'color_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function size_type()
    {
        return $this->belongsTo(SizeType::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
