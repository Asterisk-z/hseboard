<?php

namespace App\Observers;

use App\Models\MainAudit;

class MainAuditObserver
{
    /**
     * Handle the MainAudit "creating" event.
     *
     * @param  \App\Models\MainAudit  $mainAudit
     * @return void
     */
    public function creating(MainAudit $mainAudit)
    {
        $mainAudit->uuid = createUuid($mainAudit);

    }

    /**
     * Handle the MainAudit "created" event.
     *
     * @param  \App\Models\MainAudit  $mainAudit
     * @return void
     */
    public function created(MainAudit $mainAudit)
    {

    }

    /**
     * Handle the MainAudit "updated" event.
     *
     * @param  \App\Models\MainAudit  $mainAudit
     * @return void
     */
    public function updated(MainAudit $mainAudit)
    {
        //
    }

    /**
     * Handle the MainAudit "deleted" event.
     *
     * @param  \App\Models\MainAudit  $mainAudit
     * @return void
     */
    public function deleted(MainAudit $mainAudit)
    {
        //
    }

    /**
     * Handle the MainAudit "restored" event.
     *
     * @param  \App\Models\MainAudit  $mainAudit
     * @return void
     */
    public function restored(MainAudit $mainAudit)
    {
        //
    }

    /**
     * Handle the MainAudit "force deleted" event.
     *
     * @param  \App\Models\MainAudit  $mainAudit
     * @return void
     */
    public function forceDeleted(MainAudit $mainAudit)
    {
        //
    }

}
