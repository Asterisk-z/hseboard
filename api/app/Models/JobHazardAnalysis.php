<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobHazardAnalysis extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $with = ['preparedUser', 'organization', 'reviewerUser', 'steps'];
    protected $appends = ['is_completed', 'is_ongoing', 'is_declined', 'is_approved'];

    public function getIsCompletedAttribute()
    {
        return $this->status == 'completed';
    }

    public function getIsOngoingAttribute()
    {
        return $this->status == 'ongoing';
    }

    public function getIsDeclinedAttribute()
    {
        return $this->status == 'declined';
    }

    public function getIsApprovedAttribute()
    {
        return $this->status == 'approved';
    }

    public function preparedUser()
    {
        return $this->hasOne(User::class, 'id', 'prepared_by');
    }

    public function reviewerUser()
    {
        return $this->hasOne(User::class, 'id', 'reviewed_by');
    }

    public function organization()
    {
        return $this->hasOne(Organisation::class, 'id', 'organization_id');
    }

    public function steps()
    {
        return $this->hasMany(JobHazardAnalysisStep::class, 'job_hazard_analysis_id', 'id')->where('is_del', 'no');
    }

}
