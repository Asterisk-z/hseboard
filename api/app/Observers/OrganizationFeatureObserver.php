<?php

namespace App\Observers;

use App\Models\OrganizationFeature;

class OrganizationFeatureObserver
{
    /**
     * Handle the OrganizationFeature "creating" event.
     *
     * @param  \App\Models\OrganizationFeature  $organizationFeature
     * @return void
     */
    public function creating(OrganizationFeature $organizationFeature)
    {
        $organizationFeature->uuid = createUuid($organizationFeature);

    }
}
