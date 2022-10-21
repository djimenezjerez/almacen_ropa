<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'document',
        'document_type_id',
        'address',
        'email',
        'phone',
        'city_id',
    ];

    public $timestamps = true;

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function document_type()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(mb_strtolower($value));
    }
}
