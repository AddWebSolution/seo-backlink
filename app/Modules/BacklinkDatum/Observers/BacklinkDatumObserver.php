<?php

namespace App\Modules\BacklinkDatum\Observers;

use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumStoreEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumStoreEvent;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumUpdateEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumUpdateEvent;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumDeleteEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumDeleteEvent;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumRestoreEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumRestoreEvent;
use App\Modules\BacklinkDatum\Models\BacklinkDatum;

class BacklinkDatumObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(BacklinkDatum $backlinkdatum) : void
    {
        BeforeBacklinkDatumStoreEvent::dispatch($backlinkdatum);
    }
    /**
     * Handle the BacklinkDatum "created" event.
     */
    public function created(BacklinkDatum $backlinkdatum): void
    {
        AfterBacklinkDatumStoreEvent::dispatch($backlinkdatum);
    }


    /**
	 * Handle the BacklinkDatum "updating" event.
	 */
	public function updating(BacklinkDatum $backlinkdatum): void
	{
		BeforeBacklinkDatumUpdateEvent::dispatch($backlinkdatum);
	}

    /**
     * Handle the BacklinkDatum "updated" event.
     */
    public function updated(BacklinkDatum $backlinkdatum): void
    {
        AfterBacklinkDatumUpdateEvent::dispatch($backlinkdatum);
    }


    /**
	 * Handle the BacklinkDatum "deleting" event.
	 */
	public function deleting(BacklinkDatum $backlinkdatum): void
	{
		BeforeBacklinkDatumDeleteEvent::dispatch($backlinkdatum);
	}

    /**
     * Handle the BacklinkDatum "deleted" event.
     */
    public function deleted(BacklinkDatum $backlinkdatum): void
    {
        AfterBacklinkDatumDeleteEvent::dispatch($backlinkdatum);
    }

    /**
	 * Handle the BacklinkDatum "restoring" event.
	 */
	public function restoring(BacklinkDatum $backlinkdatum): void
	{
		BeforeBacklinkDatumRestoreEvent::dispatch($backlinkdatum);
	}

    /**
     * Handle the BacklinkDatum "restored" event.
     */
    public function restored(BacklinkDatum $backlinkdatum): void
    {
        AfterBacklinkDatumRestoreEvent::dispatch($backlinkdatum);
    }

    /**
     * Handle the BacklinkDatum "force deleted" event.
     */
    public function forceDeleted(BacklinkDatum $backlinkdatum): void
    {
        //
    }
}
