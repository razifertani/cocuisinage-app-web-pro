<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class FCMNotification extends Model
{
    protected $table = 'fcm_notifications';

    protected $fillable = [
        'establishment_id',
        'sender_id',
        'receiver_id',
        'notification_type_id',
        'title',
        'body',
    ];

    protected $appends = [
        'created_at_difference_for_humans',
        'image_url',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function getCreatedAtDifferenceForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getImageUrlAttribute()
    {
        try {
            if ($this->sender->profile_photo_path != null) {
                $link = Storage::cloud()->temporaryUrl(
                    'professionals/' . $this->sender->id . '/' . $this->sender->profile_photo_path,
                    now()->addMinutes(30),
                );
                return $link;
            } else {
                return "https://static.vecteezy.com/system/resources/previews/002/275/818/non_2x/female-avatar-woman-profile-icon-for-network-vector.jpg";
            }
        } catch (\Throwable$th) {
            report($th);
            return "https://static.vecteezy.com/system/resources/previews/002/275/818/non_2x/female-avatar-woman-profile-icon-for-network-vector.jpg";
        }
    }

    public function notification_type()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function sender()
    {
        return $this->belongsTo(Professional::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Professional::class, 'receiver_id');
    }
}
