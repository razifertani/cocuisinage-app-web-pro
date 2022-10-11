<?php

namespace App\Models;

use App\Services\FCMService;
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

    protected $table = "professional";

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
        'is_owner',
        'fcm_token',
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

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getImageUrlAttribute()
    {
        try {
            if ($this->profile_photo_path != null) {
                $link = Storage::cloud()->temporaryUrl(
                    'professionals/' . auth()->user()?->id . '/' . $this->profile_photo_path,
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

    public function attach_role($establishment_id, $role_id)
    {
        $this->establishments_roles()->attach(
            $establishment_id,
            [
                'role_id' => $role_id,
            ],
        );
        if ($role_id == config('cocuisinage.role_owner_id')) {
            $this->permissions()->attach(
                Permission::all(),
                [
                    'establishment_id' => $establishment_id,
                ],
            );
        }
    }

    public function toggle_permission($establishment_id, $permission_id)
    {
        if ($this->permissions->contains($permission_id)) {

            $this->permissions()->detach(
                $permission_id,
                [
                    'establishment_id' => $establishment_id,
                ],
            );

            (new FCMService())->sendFCM($establishment_id, auth()->user()->id, $this->id, config('cocuisinage.notifications_types.permission'), 'Permission révoquée', 'La permission ' . Permission::findOrFail($permission_id)->name . ' vous a été révoquée !');

        } else {

            $this->permissions()->attach(
                $permission_id,
                [
                    'establishment_id' => $establishment_id,
                ],
            );

            (new FCMService())->sendFCM($establishment_id, auth()->user()->id, $this->id, config('cocuisinage.notifications_types.permission'), 'Permission accordée', 'La permission ' . Permission::findOrFail($permission_id)->name . ' vous a été accordée !');
        }
    }

    public function toggle_notification_type_active_param($establishment_id, $notification_type_id)
    {
        $fcm_type_active = $this->notifications_params()->where('notifications_types.id', $notification_type_id)->wherePivot('establishment_id', $establishment_id)->first()->pivot->active;

        if ($fcm_type_active) {

            $this->notifications_params()->wherePivot('establishment_id', $establishment_id)->updateExistingPivot($notification_type_id, ['active' => false]);

        } else {

            $this->notifications_params()->wherePivot('establishment_id', $establishment_id)->updateExistingPivot($notification_type_id, ['active' => true]);

        }
    }

    public function canManagePermission($establishment_id, $permission_id)
    {
        return $this->establishments_permissions()
            ->where('establishment_id', $establishment_id)
            ->where('permission_id', $permission_id)
            ->count() > 0;
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

    public function notifications_params()
    {
        return $this->belongsToMany(NotificationType::class, 'professional_notifications_params')->withPivot('establishment_id', 'active');
    }

    public function notifications_as_sender()
    {
        return $this->hasMany(FCMNotification::class, 'sender_id');
    }

    public function notifications_as_receiver()
    {
        return $this->hasMany(FCMNotification::class, 'receiver_id');
    }
}
