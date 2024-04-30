<?php

namespace App\Providers;

use App\Models\Action;
use App\Models\Feature;
use App\Models\Inspection;
use App\Models\Investigation;
use App\Models\JobHazardAnalysis;
use App\Models\MainAudit;
use App\Models\Observation;
use App\Models\Offer;
use App\Models\Organisation;
use App\Models\OrganizationFeature;
use App\Models\Statistics;
use App\Models\Subscription;
use App\Models\User;
use App\Observers\ActionObserver;
use App\Observers\FeatureObserver;
use App\Observers\InspectionObserver;
use App\Observers\InvestigationObserver;
use App\Observers\JobHazardAnalysisObserver;
use App\Observers\MainAuditObserver;
use App\Observers\ObservationObserver;
use App\Observers\OfferObserver;
use App\Observers\OrganisationObserver;
use App\Observers\OrganizationFeatureObserver;
use App\Observers\StatisticObserver;
use App\Observers\SubscriptionObserver;
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
        Statistics::observe(StatisticObserver::class);
        Investigation::observe(InvestigationObserver::class);
        MainAudit::observe(MainAuditObserver::class);
        Feature::observe(FeatureObserver::class);
        Subscription::observe(SubscriptionObserver::class);
        OrganizationFeature::observe(OrganizationFeatureObserver::class);
        Inspection::observe(InspectionObserver::class);
        JobHazardAnalysis::observe(JobHazardAnalysisObserver::class);
    }
}
