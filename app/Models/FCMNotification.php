<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FCMNotification extends Model
{
    protected $table = "fcm_notifications";

    protected $fillable = [
        'professional_id',
        'title',
        'body',
    ];

    protected $appends = [
        'created_at_difference_for_humans',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function getCreatedAtDifferenceForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }
}
