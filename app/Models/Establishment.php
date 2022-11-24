<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;
use Storage;

class Establishment extends Model
{

    protected $fillable = [
        'company_id',
        'name',
        'city',
        'longitude',
        'latitude',
        'img',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        try {
            if ($this->img != null) {
                $link = Storage::cloud()->temporaryUrl(
                    'professionals/' . $this->owner()->id . '/' . $this->img,
                    now()->addMinutes(30),
                );
                return $link;
            } else {
                return "https://images.wsj.net/im-581988/M";
            }
        } catch (\Throwable$th) {
            report($th);
            return "https://images.wsj.net/im-581988/M";
        }
    }

    public function professionals()
    {
        return $this->belongsToMany(Professional::class, 'professional_roles_in_establishment')->withPivot('role_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class)->orderBy('should_start_at');
    }

    public function roles()
    {
        return $this->hasMany(Role::class);
    }

    public function owner()
    {
        return $this->professionals()->wherePivot('role_id', config('cocuisinage.role_owner_id'))->first();
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

}
