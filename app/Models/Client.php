<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active',
        'user_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
