<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commandes';

    protected $fillable = [
        'particulier_id',
        'montant',
        'type_livraison',
        'establishment_id',
        'status',
        'comming_hour',
        'receved_hour',
        'ship_type',
        'message',
    ];


    public function particulier()
    {
        return $this->belongsTo(User::class, 'particulier_id');
    }
    public function establishment()
    {
        return $this->belongsTo(Establishment::class, 'establishment_id');
    }

    public function commandeProduct()
    {
        return $this->hasMany(CommandProduct::class, 'commande_id');
    }

    // public function product()
    // {
    //     return $this->belongsToMany(CommandProduct::class, 'commande_product', 'establishment_product_id');
    // }
}
