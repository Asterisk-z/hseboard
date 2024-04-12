<?php

namespace App\Observers;

use App\Models\Statistics;

class StatisticObserver
{
    //
    /**
     * Handle the Statistics "creating" event.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return void
     */
    public function creating(Statistics $statistics)
    {
        $statistics->uuid = createUuid($statistics);

    }

    /**
     * Handle the Statistics "created" event.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return void
     */
    public function created(Statistics $statistics)
    {

    }

    /**
     * Handle the Statistics "updated" event.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return void
     */
    public function updated(Statistics $statistics)
    {
        //
    }

    /**
     * Handle the Statistics "deleted" event.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return void
     */
    public function deleted(Statistics $statistics)
    {
        //
    }

    /**
     * Handle the Statistics "restored" event.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return void
     */
    public function restored(Statistics $statistics)
    {
        //
    }

    /**
     * Handle the Statistics "force deleted" event.
     *
     * @param  \App\Models\Statistics  $statistics
     * @return void
     */
    public function forceDeleted(Statistics $statistics)
    {
        //
    }
}
