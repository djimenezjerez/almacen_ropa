<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferDetail extends Model
{
    protected $fillable = [
        'stock_transfer_id',
        'product_id',
        'stock',
    ];

    public $timestamps = true;

    public function stock_transfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
