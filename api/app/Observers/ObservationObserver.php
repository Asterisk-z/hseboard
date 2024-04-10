<?php

namespace App\Observers;

use App\Models\Observation;

class ObservationObserver
{
    /**
     * Handle the Observation "creating" event.
     *
     * @param  \App\Models\Observation  $observation
     * @return void
     */
    public function creating(Observation $observation)
    {
        $observation->uuid = createUuid($observation);

    }

    /**
     * Handle the Observation "created" event.
     *
     * @param  \App\Models\Observation  $observation
     * @return void
     */
    public function created(Observation $observation)
    {

    }

    /**
     * Handle the Observation "updated" event.
     *
     * @param  \App\Models\Observation  $observation
     * @return void
     */
    public function updated(Observation $observation)
    {
        //
    }

    /**
     * Handle the Observation "deleted" event.
     *
     * @param  \App\Models\Observation  $observation
     * @return void
     */
    public function deleted(Observation $observation)
    {
        //
    }

    /**
     * Handle the Observation "restored" event.
     *
     * @param  \App\Models\Observation  $observation
     * @return void
     */
    public function restored(Observation $observation)
    {
        //
    }

    /**
     * Handle the Observation "force deleted" event.
     *
     * @param  \App\Models\Observation  $observation
     * @return void
     */
    public function forceDeleted(Observation $observation)
    {
        //
    }
}
