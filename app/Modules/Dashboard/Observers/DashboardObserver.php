<?php

namespace App\Modules\Dashboard\Observers;

use App\Modules\Dashboard\Events\AfterDashboardStoreEvent;
use App\Modules\Dashboard\Events\BeforeDashboardStoreEvent;
use App\Modules\Dashboard\Events\AfterDashboardUpdateEvent;
use App\Modules\Dashboard\Events\BeforeDashboardUpdateEvent;
use App\Modules\Dashboard\Events\AfterDashboardDeleteEvent;
use App\Modules\Dashboard\Events\BeforeDashboardDeleteEvent;
use App\Modules\Dashboard\Events\AfterDashboardRestoreEvent;
use App\Modules\Dashboard\Events\BeforeDashboardRestoreEvent;
use App\Modules\Dashboard\Models\Dashboard;

class DashboardObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(Dashboard $dashboard) : void
    {
        BeforeDashboardStoreEvent::dispatch($dashboard);
    }
    /**
     * Handle the Dashboard "created" event.
     */
    public function created(Dashboard $dashboard): void
    {
        AfterDashboardStoreEvent::dispatch($dashboard);
    }


    /**
	 * Handle the Dashboard "updating" event.
	 */
	public function updating(Dashboard $dashboard): void
	{
		BeforeDashboardUpdateEvent::dispatch($dashboard);
	}

    /**
     * Handle the Dashboard "updated" event.
     */
    public function updated(Dashboard $dashboard): void
    {
        AfterDashboardUpdateEvent::dispatch($dashboard);
    }


    /**
	 * Handle the Dashboard "deleting" event.
	 */
	public function deleting(Dashboard $dashboard): void
	{
		BeforeDashboardDeleteEvent::dispatch($dashboard);
	}

    /**
     * Handle the Dashboard "deleted" event.
     */
    public function deleted(Dashboard $dashboard): void
    {
        AfterDashboardDeleteEvent::dispatch($dashboard);
    }

    /**
	 * Handle the Dashboard "restoring" event.
	 */
	public function restoring(Dashboard $dashboard): void
	{
		BeforeDashboardRestoreEvent::dispatch($dashboard);
	}

    /**
     * Handle the Dashboard "restored" event.
     */
    public function restored(Dashboard $dashboard): void
    {
        AfterDashboardRestoreEvent::dispatch($dashboard);
    }

    /**
     * Handle the Dashboard "force deleted" event.
     */
    public function forceDeleted(Dashboard $dashboard): void
    {
        //
    }
}
