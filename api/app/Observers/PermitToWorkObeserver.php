<?php

namespace App\Observers;

use App\Models\PermitToWork;

class PermitToWorkObeserver
{
    /**
     * Handle the permit "creating" event.
     *
     * @param  \App\Models\PermitToWork  $permit
     * @return void
     */
    public function creating(PermitToWork $permit)
    {
        $permit->uuid = createUuid($permit);
    }

}
