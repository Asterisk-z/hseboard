<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribedFeature extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['plan', 'org_feature', 'currency'];

    public function plan()
    {
        return $this->hasOne(SubscriptionPlan::class, 'id', 'plan_id');
    }
    public function org_feature()
    {
        return $this->hasOne(OrganizationFeature::class, 'id', 'organization_feature_id');
    }
    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
}
