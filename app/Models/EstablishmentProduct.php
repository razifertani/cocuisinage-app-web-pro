<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EstablishmentProduct extends Model
{

    protected $table = 'establishment_product';


    protected $fillable = [
        'establishment_id',
        'product_id',
        'is_rec',
        'is_ing',
        'location',
        'qte_for_one_rec',
        'establishment_product_recette_id',
        'unit',
        'price_by_unit',
        'ref',
        'dlc',
        'stock_quantity',
        'img',
        'show_home'
    ];
    protected $appends = ['image_url' ];

    public function getImageUrlAttribute()
    {


        if ($this->img != null) {

            return  Storage::disk('s3')->temporaryUrl(
                "establissement/$this->establishment_id/product/$this->img",
                now()->addWeek(1)
            );
        } else {
            return $this->product->getImageUrlAttribute();
        }
    }

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id');
    }
    public function establishment()
    {
        return $this->belongsTo(Establishment::class , 'establishment_id');
    }
    public function ingrediants()
    {
        return $this->hasMany(EstablishmentProduct::class , 'establishment_product_recette_id');
    }

    public function likesUsers()
    {
        return $this->belongsToMany(User::class, 'user_establishmentproduct_likes')->withTimestamps();
    }
}

