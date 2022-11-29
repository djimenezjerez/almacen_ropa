<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'icon',
        'active',
        'entry',
        'order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'entry' => 'boolean',
    ];

    public $timestamps = false;

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = trim(mb_strtoupper($value));
    }
}
