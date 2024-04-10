<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\Observation;
use App\Models\Offer;
use App\Models\Organisation;
use App\Models\User;
use App\Observers\ActionObserver;
use App\Observers\ObservationObserver;
use App\Observers\OfferObserver;
use App\Observers\OrganisationObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Organisation::observe(OrganisationObserver::class);
        User::observe(UserObserver::class);
        Offer::observe(OfferObserver::class);
        Observation::observe(ObservationObserver::class);
        Action::observe(ActionObserver::class);
    }
}
