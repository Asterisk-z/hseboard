<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['sub_features', 'org_feature', 'currency'];

    public function sub_features()
    {
        return $this->hasMany(Feature::class, 'parent_id', 'id');
    }
    public function org_feature()
    {
        $organization = Organisation::where('uuid', request('organization_id'))->first();
        return $this->hasOne(OrganizationFeature::class, 'feature_id', 'id')->where('organization_id', $organization->id);
    }
    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }
}
