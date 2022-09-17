<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    protected $fillable = [
        "professional_id",
        "establishment_id",
        "day",
        "start_at",
        "stop_at",
        "is_monthly",
        "day_of_week",
    ];

    protected $dates = [
        'day',
    ];

    // protected $casts = [
    //     'start_at' => 'datetime:hh:mm',
    //     'stop_at' => 'datetime:hh:mm',
    // ];

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
