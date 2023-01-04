<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{

    protected $appends = [ 'image_url' ,'cov_url' ];
    protected $fillable  = [ 'default_tag_id' , 'is_verified' , 'name'];


    public function getImageUrlAttribute()
    {
        if ($this->img != null && $this->hasImage == false) {
            return  Storage::disk('s3')->temporaryUrl(
                "produit/$this->img",
                now()->addWeek(1)
            );
        } else if ($this->hasImage && $this->code_bar != null && $this->img != null)
            return $this->img;
        else if ($this->defaultTag()->exists())
            return $this->defaultTag->getImageUrlAttribute();
        else
            return "https://archive.org/download/no-photo-available/no-photo-available.png";
    }


     public function getCovUrlAttribute(): string
    {


            if ($this->cov_image != null){

                return  Storage::disk('s3')->temporaryUrl(
                    "produit/$this->cov_image",
                    now()->addWeek(1)
                );
            } else {
                return $this->getImageUrlAttribute();
            }


    }
    public function tag()
    {
        return $this->belongsToMany('App\Models\Tag', 'products_tags');
    }
    public function recipes()
    {
        return $this->hasMany(Recipe::class, 'product_id');
    }

    public function defaultTag()
    {
        return $this->hasOne(Tag::class , 'default_product_id');
    }

    public function establishments()
    {
        return $this->belongsToMany(Establishment::class, 'establishment_product')->withPivot('id', 'unit', 'price_by_unit', 'location', 'stock_quantity');
    }


    public function properties()
    {
        return $this->belongsToMany('App\Models\Property', 'products_properties')->withPivot('value', 'ratio', 'unit');
    }



}
