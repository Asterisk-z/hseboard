<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationFeature extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['is_active', 'feature_name'];

    public function getIsActiveAttribute()
    {
        return $this->end_date && $this->end_date > now();
    }

    public function getFeatureNameAttribute()
    {
        $feature = Feature::where('id', $this->feature_id)->first('description');
        return $feature->description;
    }
}
