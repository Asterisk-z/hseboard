<?php

namespace App\Observers;

use App\Models\Feature;

class FeatureObserver
{
    //
    /**
     * Handle the action "creating" event.
     *
     * @param  \App\Models\Action  $action
     * @return void
     */
    public function creating(Feature $action)
    {
        $action->uuid = createUuid($action);

    }
}
