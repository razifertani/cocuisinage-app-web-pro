<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{

    protected $fillable = [
        "email",
        "invitation_token",
        "url_token",
        "registered_at",
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
