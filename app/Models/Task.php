<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $fillable = [
        "professional_id",
        "establishment_id",
        "planning_id",
        "name",
        "status",
        "comment",
        "image_link",
    ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}
