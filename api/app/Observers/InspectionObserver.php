<?php

namespace App\Observers;

use App\Models\Inspection;

class InspectionObserver
{
    /**
     * Handle the inspec$inspection "creating" event.
     *
     * @param  \App\Models\inspec$inspection  $inspection
     * @return void
     */
    public function creating(Inspection $inspection)
    {
        $inspection->uuid = createUuid($inspection);

    }
}
