<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'comment',
        'movement_type',
        'user_id',
        'client_id',
    ];

    public $timestamps = true;

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

    public function fromable()
    {
        return $this->morphTo();
    }

    public function toable()
    {
        return $this->morphTo();
    }
}
