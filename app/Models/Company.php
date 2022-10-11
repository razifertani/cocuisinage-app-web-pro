<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        "name",
        "email",
        "phone_number",
        "rib",
        "siret",
    ];

    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }

    public function professionals()
    {
        return $this->hasMany(Professional::class);
    }
}
