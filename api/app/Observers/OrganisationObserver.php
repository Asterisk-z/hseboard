<?php

namespace App\Observers;

use App\Models\Organisation;

class OrganisationObserver
{
    /**
     * Handle the Organisation "creating" event.
     *
     * @param  \App\Models\Organisation  $Organisation
     * @return void
     */
    public function creating(Organisation $organisation)
    {
        $organisation->uuid = createUuid($organisation);
        $organisation->token = $organisation->getToken();
    }

    /**
     * Handle the Organisation "created" event.
     *
     * @param  \App\Models\Organisation  $Organisation
     * @return void
     */
    public function created(Organisation $Organisation)
    {

    }

    /**
     * Handle the Organisation "updated" event.
     *
     * @param  \App\Models\Organisation  $Organisation
     * @return void
     */
    public function updated(Organisation $Organisation)
    {
        //
    }

    /**
     * Handle the Organisation "deleted" event.
     *
     * @param  \App\Models\Organisation  $Organisation
     * @return void
     */
    public function deleted(Organisation $Organisation)
    {
        //
    }

    /**
     * Handle the Organisation "restored" event.
     *
     * @param  \App\Models\Organisation  $Organisation
     * @return void
     */
    public function restored(Organisation $Organisation)
    {
        //
    }

    /**
     * Handle the Organisation "force deleted" event.
     *
     * @param  \App\Models\Organisation  $Organisation
     * @return void
     */
    public function forceDeleted(Organisation $Organisation)
    {
        //
    }

    // 'retrieved', 'creating', 'created', 'updating', 'updated',
    //  'saving', 'saved', 'restoring', 'restored', 'replicating',
    //   'deleting', 'deleted', 'forceDeleted', 'trashed'
}
