<?php

namespace App\Observers;

use App\Models\Subscription;

class SubscriptionObserver
{
    /**
     * Handle the subscription "creating" event.
     *
     * @param  \App\Models\subscription  $subscription
     * @return void
     */
    public function creating(Subscription $subscription)
    {
        $subscription->uuid = createUuid($subscription);

    }
}
