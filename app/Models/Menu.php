<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menu';

    protected $fillable = [
        'type',
        'note',
        'date',
        'user_id',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function menuTag()
    {
        return $this->hasMany(MenuTag::class, 'menu_id');
    }
    public function menuUTag()
    {
        return $this->hasMany(MenuUTag::class, 'menu_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'menu_tags' , 'tag_id');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'menu_tags' , 'product_id');
    }

    // public function product()
    // {
    //     return $this->belongsTo(Product::class , 'menu_tag' , 'product_id');
    // }
}
