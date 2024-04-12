<?php

namespace App\Observers;

use App\Models\Investigation;

class InvestigationObserver
{
    //
    /**
     * Handle the Investigation "creating" event.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return void
     */
    public function creating(Investigation $investigation)
    {
        $investigation->uuid = createUuid($investigation);

    }

    /**
     * Handle the Investigation "created" event.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return void
     */
    public function created(Investigation $investigation)
    {

    }

    /**
     * Handle the Investigation "updated" event.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return void
     */
    public function updated(Investigation $investigation)
    {
        //
    }

    /**
     * Handle the Investigation "deleted" event.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return void
     */
    public function deleted(Investigation $investigation)
    {
        //
    }

    /**
     * Handle the Investigation "restored" event.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return void
     */
    public function restored(Investigation $investigation)
    {
        //
    }

    /**
     * Handle the Investigation "force deleted" event.
     *
     * @param  \App\Models\Investigation  $investigation
     * @return void
     */
    public function forceDeleted(Investigation $investigation)
    {
        //
    }
}
