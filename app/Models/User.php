<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{

    use  Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
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
        'fcm_token',
    ];
    // protected $withCount = ['recipes'];
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

    protected $appends = ['image_url' , 'cov_url'];
    public function getCovUrlAttribute()
    {


        if ($this->cov_photo_path != null) {

            return  Storage::disk('s3')->temporaryUrl(
                "users/$this->id/$this->cov_photo_path",
                now()->addMinutes(30)
            );
        } else {
            return asset('/assets/img/cover.svg');
        }
    }
    public function getImageUrlAttribute(): string
    {


        if ($this->profile_photo_path != null) {

            return  Storage::disk('s3')->temporaryUrl(
                "users/$this->id/$this->profile_photo_path",
                now()->addMinutes(30)
            );
        } else {
            return "https://eu.ui-avatars.com/api/?background=FFAC1C&color=fff&bold=true&name=".$this->name."+".$this->last_name;
        }
    }


    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withPivot('type');
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'creator_id');
    }

    public function likesRecipe()
    {
        return $this->belongsToMany(Recipe::class, 'user_recipe_likes')->withTimestamps();
    }
    public function likesEstablishment()
    {
        return $this->belongsToMany(Establishment::class, 'user_establishment_likes');
    }
    public function likesProduct()
    {
        return $this->belongsToMany(Product::class, 'user_product_likes')->withTimestamps();
    }
    public function likesEstablishmentProduct()
    {
        return $this->belongsToMany(EstablishmentProduct::class, 'user_establishmentproduct_likes')->withTimestamps();
    }

    public function likedByUsers(){
        return $this->belongsToMany(User::class, 'user_user_likes' , 'user_id')->withTimestamps();
    }

    public function likes(){
        return $this->belongsToMany(User::class, 'user_user_likes' , 'receiver_user_id' )->withTimestamps();
    }
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
