<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Storage;

class Professional extends Authenticatable
{
    use HasRoles;
    use HasApiTokens;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'address_line_one',
        'address_line_two',
        'country',
        'state',
        'zip_code',
        'profile_photo_path',
        'cov_photo_path',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->profile_photo_path != null) {
            $link = Storage::cloud()->temporaryUrl(
                'professionals/' . auth()->user()->id . '/' . $this->profile_photo_path,
                now()->addMinutes(30),
            );
            return $link;
        } else {
            return "https://static.vecteezy.com/system/resources/previews/002/275/818/non_2x/female-avatar-woman-profile-icon-for-network-vector.jpg";
        }
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'professional_roles_in_establishment')->withPivot('establishment_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'professional_permissions_in_establishment')->withPivot('establishment_id');
    }

    public function establishments_roles()
    {
        return $this->belongsToMany(Establishment::class, 'professional_roles_in_establishment')->withPivot('role_id');
    }

    public function establishments_permissions()
    {
        return $this->belongsToMany(Establishment::class, 'professional_permissions_in_establishment')->withPivot('permission_id');
    }

    public function plannings()
    {
        return $this->hasMany(Planning::class)->orderBy('should_start_at');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
