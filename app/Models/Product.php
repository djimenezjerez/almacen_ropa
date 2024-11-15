<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active',
        'product_name_id',
        'brand_id',
        'gender_id',
        'size_id',
        'color_id',
        'stock',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function name()
    {
        return $this->belongsTo(ProductName::class, 'product_name_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function sizeType(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->size->size_type,
        );
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function movements()
    {
        return $this->hasMany(MovementDetail::class);
    }

    public function shopping_cart_products()
    {
        return $this->hasMany(ShoppingCartProduct::class);
    }

    protected function image(): Attribute
    {
        $image = ProductImage::where('color_id', $this->color->id)->where('product_name_id', $this->name->id)->first();
        return Attribute::make(
            get: fn() => $image ? $image->path : null,
        );
    }
}
