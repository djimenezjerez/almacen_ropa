<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active',
        'person_id',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public $timestamps = true;

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
