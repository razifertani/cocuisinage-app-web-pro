<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [

        'establishment_id',

        'name',
        'nb_people',
    ];

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function is_free_for_day_and_hour($day, $hour)
    {
        $reservations = Table::reservations()->where([
            ['establishment_id', $this->establishment_id],
            ['day', $day],
        ])->get();

        foreach ($reservations as $reservation) {
            if (!(
                Carbon::createFromTimeString($hour)->isAfter((Carbon::createFromTimeString($reservation->hour))->addHour())
                ||
                Carbon::createFromTimeString($hour)->addHour()->isBefore(Carbon::createFromTimeString($reservation->hour))
            )) {
                return false;
            }
        }

        return true;
    }
}
