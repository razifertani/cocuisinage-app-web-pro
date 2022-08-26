<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// use Laratrust\Traits\LaratrustUserTrait;

class Professional extends Authenticatable
{

    use HasApiTokens;
    use Notifiable;
    // use LaratrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'professional';

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

    public function parent()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'pro_id');

    }

    public function likesRecipe()
    {
        return $this->belongsToMany(Recipe::class, 'pro_rec_likes')->withTimestamps();

    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
