<?php

namespace App\Observers;

use App\Models\JobHazardAnalysis;

class JobHazardAnalysisObserver
{
    /**
     * Handle the job_hazard "creating" event.
     *
     * @param  \App\Models\JobHazardAnalysis  $job_hazard
     * @return void
     */
    public function creating(JobHazardAnalysis $job_hazard)
    {
        $job_hazard->uuid = createUuid($job_hazard);

    }
}
