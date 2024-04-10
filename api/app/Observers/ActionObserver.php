<?php

namespace App\Observers;

use App\Models\Action;

class ActionObserver
{

    /**
     * Handle the Action "creating" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function creating(Action $action)
    {
        $action->uuid = createUuid($action);

    }

    /**
     * Handle the Action "created" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function created(Action $action)
    {

    }

    /**
     * Handle the Action "updated" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function updated(Action $action)
    {
        //
    }

    /**
     * Handle the Action "deleted" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function deleted(Action $action)
    {
        //
    }

    /**
     * Handle the Action "restored" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function restored(Action $action)
    {
        //
    }

    /**
     * Handle the Action "force deleted" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function forceDeleted(Action $action)
    {
        //
    }
}
