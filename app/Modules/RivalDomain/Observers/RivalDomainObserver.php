<?php

namespace App\Modules\RivalDomain\Observers;

use App\Modules\RivalDomain\Events\AfterRivalDomainStoreEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainStoreEvent;
use App\Modules\RivalDomain\Events\AfterRivalDomainUpdateEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainUpdateEvent;
use App\Modules\RivalDomain\Events\AfterRivalDomainDeleteEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainDeleteEvent;
use App\Modules\RivalDomain\Events\AfterRivalDomainRestoreEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainRestoreEvent;
use App\Modules\RivalDomain\Models\RivalDomain;

class RivalDomainObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(RivalDomain $rivaldomain) : void
    {
        BeforeRivalDomainStoreEvent::dispatch($rivaldomain);
    }
    /**
     * Handle the RivalDomain "created" event.
     */
    public function created(RivalDomain $rivaldomain): void
    {
        AfterRivalDomainStoreEvent::dispatch($rivaldomain);
    }


    /**
	 * Handle the RivalDomain "updating" event.
	 */
	public function updating(RivalDomain $rivaldomain): void
	{
		BeforeRivalDomainUpdateEvent::dispatch($rivaldomain);
	}

    /**
     * Handle the RivalDomain "updated" event.
     */
    public function updated(RivalDomain $rivaldomain): void
    {
        AfterRivalDomainUpdateEvent::dispatch($rivaldomain);
    }


    /**
	 * Handle the RivalDomain "deleting" event.
	 */
	public function deleting(RivalDomain $rivaldomain): void
	{
		BeforeRivalDomainDeleteEvent::dispatch($rivaldomain);
	}

    /**
     * Handle the RivalDomain "deleted" event.
     */
    public function deleted(RivalDomain $rivaldomain): void
    {
        AfterRivalDomainDeleteEvent::dispatch($rivaldomain);
    }

    /**
	 * Handle the RivalDomain "restoring" event.
	 */
	public function restoring(RivalDomain $rivaldomain): void
	{
		BeforeRivalDomainRestoreEvent::dispatch($rivaldomain);
	}

    /**
     * Handle the RivalDomain "restored" event.
     */
    public function restored(RivalDomain $rivaldomain): void
    {
        AfterRivalDomainRestoreEvent::dispatch($rivaldomain);
    }

    /**
     * Handle the RivalDomain "force deleted" event.
     */
    public function forceDeleted(RivalDomain $rivaldomain): void
    {
        //
    }
}
