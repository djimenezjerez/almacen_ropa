<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'active',
        'city_id',
        'user_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class, User::class);
    }
}
