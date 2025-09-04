<?php

namespace App\Modules\Report\Observers;

use App\Modules\Report\Events\AfterReportStoreEvent;
use App\Modules\Report\Events\BeforeReportStoreEvent;
use App\Modules\Report\Events\AfterReportUpdateEvent;
use App\Modules\Report\Events\BeforeReportUpdateEvent;
use App\Modules\Report\Events\AfterReportDeleteEvent;
use App\Modules\Report\Events\BeforeReportDeleteEvent;
use App\Modules\Report\Events\AfterReportRestoreEvent;
use App\Modules\Report\Events\BeforeReportRestoreEvent;
use App\Modules\Report\Models\Report;

class ReportObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(Report $report) : void
    {
        BeforeReportStoreEvent::dispatch($report);
    }
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        AfterReportStoreEvent::dispatch($report);
    }


    /**
	 * Handle the Report "updating" event.
	 */
	public function updating(Report $report): void
	{
		BeforeReportUpdateEvent::dispatch($report);
	}

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        AfterReportUpdateEvent::dispatch($report);
    }


    /**
	 * Handle the Report "deleting" event.
	 */
	public function deleting(Report $report): void
	{
		BeforeReportDeleteEvent::dispatch($report);
	}

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        AfterReportDeleteEvent::dispatch($report);
    }

    /**
	 * Handle the Report "restoring" event.
	 */
	public function restoring(Report $report): void
	{
		BeforeReportRestoreEvent::dispatch($report);
	}

    /**
     * Handle the Report "restored" event.
     */
    public function restored(Report $report): void
    {
        AfterReportRestoreEvent::dispatch($report);
    }

    /**
     * Handle the Report "force deleted" event.
     */
    public function forceDeleted(Report $report): void
    {
        //
    }
}
