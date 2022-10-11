<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $table = 'notifications_types';

    protected $fillable = [
        'name',
        'slug',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_notifications_params');
    }
}
