<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Task extends Model
{

    protected $fillable = [
        "professional_id",
        "establishment_id",
        "planning_id",
        "name",
        "status",
        "comment",
        "image",
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image != null) {
            $link = Storage::cloud()->temporaryUrl(
                'professionals/' . auth()->user()->id . '/' . $this->image,
                now()->addMinutes(30),
            );
            return $link;
        } else {
            return null;
        }
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}
