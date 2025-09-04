<?php

namespace App\Modules\Backlinkreport\Observers;

use App\Modules\Backlinkreport\Events\AfterBacklinkreportStoreEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportStoreEvent;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportUpdateEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportUpdateEvent;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportDeleteEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportDeleteEvent;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportRestoreEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportRestoreEvent;
use App\Modules\Backlinkreport\Models\Backlinkreport;

class BacklinkreportObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(Backlinkreport $backlinkreport) : void
    {
        BeforeBacklinkreportStoreEvent::dispatch($backlinkreport);
    }
    /**
     * Handle the Backlinkreport "created" event.
     */
    public function created(Backlinkreport $backlinkreport): void
    {
        AfterBacklinkreportStoreEvent::dispatch($backlinkreport);
    }


    /**
	 * Handle the Backlinkreport "updating" event.
	 */
	public function updating(Backlinkreport $backlinkreport): void
	{
		BeforeBacklinkreportUpdateEvent::dispatch($backlinkreport);
	}

    /**
     * Handle the Backlinkreport "updated" event.
     */
    public function updated(Backlinkreport $backlinkreport): void
    {
        AfterBacklinkreportUpdateEvent::dispatch($backlinkreport);
    }


    /**
	 * Handle the Backlinkreport "deleting" event.
	 */
	public function deleting(Backlinkreport $backlinkreport): void
	{
		BeforeBacklinkreportDeleteEvent::dispatch($backlinkreport);
	}

    /**
     * Handle the Backlinkreport "deleted" event.
     */
    public function deleted(Backlinkreport $backlinkreport): void
    {
        AfterBacklinkreportDeleteEvent::dispatch($backlinkreport);
    }

    /**
	 * Handle the Backlinkreport "restoring" event.
	 */
	public function restoring(Backlinkreport $backlinkreport): void
	{
		BeforeBacklinkreportRestoreEvent::dispatch($backlinkreport);
	}

    /**
     * Handle the Backlinkreport "restored" event.
     */
    public function restored(Backlinkreport $backlinkreport): void
    {
        AfterBacklinkreportRestoreEvent::dispatch($backlinkreport);
    }

    /**
     * Handle the Backlinkreport "force deleted" event.
     */
    public function forceDeleted(Backlinkreport $backlinkreport): void
    {
        //
    }
}
