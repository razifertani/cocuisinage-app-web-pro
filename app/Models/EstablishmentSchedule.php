<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstablishmentSchedule extends Model
{

    protected $table = 'establishment_schedule';

    protected $fillable = [
        'establishment_id',
        'day',
        'begin',
        'ending',
        'second_begin',
        'second_end',
    ];

    public $timestamps = false;
}
