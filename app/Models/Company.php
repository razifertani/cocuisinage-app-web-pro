<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ["name", "email", "created_at", "updated_at"];

    public function establishments()
    {
        return $this->hasMany(Establishment::class);
    }

    public function professionals()
    {
        return $this->hasMany(Professional::class);
    }
}
