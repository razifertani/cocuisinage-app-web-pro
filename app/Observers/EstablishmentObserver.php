<?php

namespace App\Observers;

use App\Models\Establishment;
use App\Models\EstablishmentSchedule;

class EstablishmentObserver
{
    /**
     * Handle the Establishment "created" event.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return void
     */
    public function created(Establishment $establishment)
    {
        for ($i = 1; $i < 8; $i++) {
            EstablishmentSchedule::create([
                'establishment_id' => $establishment->id,
                'day' => $i,
            ]);
        }
    }

    /**
     * Handle the Establishment "updated" event.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return void
     */
    public function updated(Establishment $establishment)
    {
        //
    }

    /**
     * Handle the Establishment "deleted" event.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return void
     */
    public function deleted(Establishment $establishment)
    {
        //
    }

    /**
     * Handle the Establishment "restored" event.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return void
     */
    public function restored(Establishment $establishment)
    {
        //
    }

    /**
     * Handle the Establishment "force deleted" event.
     *
     * @param  \App\Models\Establishment  $establishment
     * @return void
     */
    public function forceDeleted(Establishment $establishment)
    {
        //
    }
}
