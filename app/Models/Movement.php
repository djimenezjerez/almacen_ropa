<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'comment',
        'movement_type_id',
        'user_id',
        'client_id',
        'from_store_id',
        'to_store_id',
    ];

    public $timestamps = true;

    public function movement_type()
    {
        return $this->belongsTo(MovementType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function details()
    {
        return $this->hasMany(MovementDetail::class);
    }

    public function from_store()
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function to_store()
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }
}
