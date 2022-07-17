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
        'address',
        'email',
        'phone',
        'city_id',
        'document_type_id',
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

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = trim(mb_strtolower($value));
    }
}
