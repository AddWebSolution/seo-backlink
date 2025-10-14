<?php

namespace App\Modules\BacklinkHistory\Observers;

use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryStoreEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryStoreEvent;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryUpdateEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryUpdateEvent;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryDeleteEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryDeleteEvent;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryRestoreEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryRestoreEvent;
use App\Modules\BacklinkHistory\Models\BacklinkHistory;

class BacklinkHistoryObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(BacklinkHistory $backlinkhistory) : void
    {
        BeforeBacklinkHistoryStoreEvent::dispatch($backlinkhistory);
    }
    /**
     * Handle the BacklinkHistory "created" event.
     */
    public function created(BacklinkHistory $backlinkhistory): void
    {
        AfterBacklinkHistoryStoreEvent::dispatch($backlinkhistory);
    }


    /**
	 * Handle the BacklinkHistory "updating" event.
	 */
	public function updating(BacklinkHistory $backlinkhistory): void
	{
		BeforeBacklinkHistoryUpdateEvent::dispatch($backlinkhistory);
	}

    /**
     * Handle the BacklinkHistory "updated" event.
     */
    public function updated(BacklinkHistory $backlinkhistory): void
    {
        AfterBacklinkHistoryUpdateEvent::dispatch($backlinkhistory);
    }


    /**
	 * Handle the BacklinkHistory "deleting" event.
	 */
	public function deleting(BacklinkHistory $backlinkhistory): void
	{
		BeforeBacklinkHistoryDeleteEvent::dispatch($backlinkhistory);
	}

    /**
     * Handle the BacklinkHistory "deleted" event.
     */
    public function deleted(BacklinkHistory $backlinkhistory): void
    {
        AfterBacklinkHistoryDeleteEvent::dispatch($backlinkhistory);
    }

    /**
	 * Handle the BacklinkHistory "restoring" event.
	 */
	public function restoring(BacklinkHistory $backlinkhistory): void
	{
		BeforeBacklinkHistoryRestoreEvent::dispatch($backlinkhistory);
	}

    /**
     * Handle the BacklinkHistory "restored" event.
     */
    public function restored(BacklinkHistory $backlinkhistory): void
    {
        AfterBacklinkHistoryRestoreEvent::dispatch($backlinkhistory);
    }

    /**
     * Handle the BacklinkHistory "force deleted" event.
     */
    public function forceDeleted(BacklinkHistory $backlinkhistory): void
    {
        //
    }
}
