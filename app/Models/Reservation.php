<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [

        'establishment_id',

        'client_name',
        'client_phone_number',
        'nb_people',
        'day',
        'hour',
        'comment',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
