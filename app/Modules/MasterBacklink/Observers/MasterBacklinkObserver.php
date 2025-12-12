<?php

namespace App\Modules\MasterBacklink\Observers;

use App\Modules\MasterBacklink\Events\AfterMasterBacklinkStoreEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkStoreEvent;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkUpdateEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkUpdateEvent;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkDeleteEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkDeleteEvent;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkRestoreEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkRestoreEvent;
use App\Modules\MasterBacklink\Models\MasterBacklink;

class MasterBacklinkObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(MasterBacklink $masterbacklink) : void
    {
        BeforeMasterBacklinkStoreEvent::dispatch($masterbacklink);
    }
    /**
     * Handle the MasterBacklink "created" event.
     */
    public function created(MasterBacklink $masterbacklink): void
    {
        AfterMasterBacklinkStoreEvent::dispatch($masterbacklink);
    }


    /**
	 * Handle the MasterBacklink "updating" event.
	 */
	public function updating(MasterBacklink $masterbacklink): void
	{
		BeforeMasterBacklinkUpdateEvent::dispatch($masterbacklink);
	}

    /**
     * Handle the MasterBacklink "updated" event.
     */
    public function updated(MasterBacklink $masterbacklink): void
    {
        AfterMasterBacklinkUpdateEvent::dispatch($masterbacklink);
    }


    /**
	 * Handle the MasterBacklink "deleting" event.
	 */
	public function deleting(MasterBacklink $masterbacklink): void
	{
		BeforeMasterBacklinkDeleteEvent::dispatch($masterbacklink);
	}

    /**
     * Handle the MasterBacklink "deleted" event.
     */
    public function deleted(MasterBacklink $masterbacklink): void
    {
        AfterMasterBacklinkDeleteEvent::dispatch($masterbacklink);
    }

    /**
	 * Handle the MasterBacklink "restoring" event.
	 */
	public function restoring(MasterBacklink $masterbacklink): void
	{
		BeforeMasterBacklinkRestoreEvent::dispatch($masterbacklink);
	}

    /**
     * Handle the MasterBacklink "restored" event.
     */
    public function restored(MasterBacklink $masterbacklink): void
    {
        AfterMasterBacklinkRestoreEvent::dispatch($masterbacklink);
    }

    /**
     * Handle the MasterBacklink "force deleted" event.
     */
    public function forceDeleted(MasterBacklink $masterbacklink): void
    {
        //
    }
}
