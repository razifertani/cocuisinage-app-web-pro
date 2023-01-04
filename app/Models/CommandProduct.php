<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandProduct extends Model
{


    public $table = 'commande_product';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'establishment_product_id',
        'qte',
        'prix',
        'commande_id',
        'comming_hour',
        'ship_type',
        'message',
        'status',

    ];



    /**
     * Get the post that owns the comment.
     */
    public function commande()
    {
        return $this->belongsTo(Commande::class , 'commande_id');
    }

    /**
     * Get the post that owns the comment.
     */
    public function establishmentProduct()
    {
        return $this->belongsTo(EstablishmentProduct::class , 'establishment_product_id');
    }
}
