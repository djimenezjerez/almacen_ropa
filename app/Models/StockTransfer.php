<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    protected $fillable = [
        'origin_store_id',
        'destiny_store_id',
        'origin_warehouse_id',
        'destiny_warehouse_id',
        'user_id',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function origin_store()
    {
        return $this->belongsTo(Store::class, 'origin_store_id', 'id');
    }

    public function destiny_store()
    {
        return $this->belongsTo(Store::class, 'destiny_store_id', 'id');
    }

    public function origin_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'origin_warehouse_id', 'id');
    }

    public function destiny_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destiny_warehouse_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(StockTransferDetail::class);
    }
}
