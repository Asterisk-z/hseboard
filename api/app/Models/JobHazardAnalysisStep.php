<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobHazardAnalysisStep extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $with = ['top_events', 'sources', 'targets', 'consequences', 'preventives', 'rcps', 'recoveries', 'parties'];

    public function top_events()
    {
        return $this->hasMany(JobHazardAnalysisStepTopEvent::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function sources()
    {
        return $this->hasMany(JobHazardAnalysisStepHazardSource::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function targets()
    {
        return $this->hasMany(JobHazardAnalysisStepTarget::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function consequences()
    {
        return $this->hasMany(JobHazardAnalysisStepConsequence::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function preventives()
    {
        return $this->hasMany(JobHazardAnalysisStepPreventiveAction::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function rcps()
    {
        return $this->hasMany(JobHazardAnalysisStepRcp::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function recoveries()
    {
        return $this->hasMany(JobHazardAnalysisStepRecoveryMeasure::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
    public function parties()
    {
        return $this->hasMany(JobHazardAnalysisStepActionParty::class, 'job_hazard_analysis_step_id', 'id')->where('is_del', 'no');
    }
}
