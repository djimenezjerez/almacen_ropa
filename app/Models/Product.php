<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsTo(ProductName::class);
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

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function movements()
    {
        return $this->hasMany(MovementDetail::class);
    }

    public function stock_transfer_details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }
}
