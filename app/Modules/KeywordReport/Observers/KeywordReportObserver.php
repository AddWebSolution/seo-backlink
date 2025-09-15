<?php

namespace App\Modules\KeywordReport\Observers;

use App\Modules\KeywordReport\Events\AfterKeywordReportStoreEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportStoreEvent;
use App\Modules\KeywordReport\Events\AfterKeywordReportUpdateEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportUpdateEvent;
use App\Modules\KeywordReport\Events\AfterKeywordReportDeleteEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportDeleteEvent;
use App\Modules\KeywordReport\Events\AfterKeywordReportRestoreEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportRestoreEvent;
use App\Modules\KeywordReport\Models\KeywordReport;

class KeywordReportObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(KeywordReport $keywordreport) : void
    {
        BeforeKeywordReportStoreEvent::dispatch($keywordreport);
    }
    /**
     * Handle the KeywordReport "created" event.
     */
    public function created(KeywordReport $keywordreport): void
    {
        AfterKeywordReportStoreEvent::dispatch($keywordreport);
    }


    /**
	 * Handle the KeywordReport "updating" event.
	 */
	public function updating(KeywordReport $keywordreport): void
	{
		BeforeKeywordReportUpdateEvent::dispatch($keywordreport);
	}

    /**
     * Handle the KeywordReport "updated" event.
     */
    public function updated(KeywordReport $keywordreport): void
    {
        AfterKeywordReportUpdateEvent::dispatch($keywordreport);
    }


    /**
	 * Handle the KeywordReport "deleting" event.
	 */
	public function deleting(KeywordReport $keywordreport): void
	{
		BeforeKeywordReportDeleteEvent::dispatch($keywordreport);
	}

    /**
     * Handle the KeywordReport "deleted" event.
     */
    public function deleted(KeywordReport $keywordreport): void
    {
        AfterKeywordReportDeleteEvent::dispatch($keywordreport);
    }

    /**
	 * Handle the KeywordReport "restoring" event.
	 */
	public function restoring(KeywordReport $keywordreport): void
	{
		BeforeKeywordReportRestoreEvent::dispatch($keywordreport);
	}

    /**
     * Handle the KeywordReport "restored" event.
     */
    public function restored(KeywordReport $keywordreport): void
    {
        AfterKeywordReportRestoreEvent::dispatch($keywordreport);
    }

    /**
     * Handle the KeywordReport "force deleted" event.
     */
    public function forceDeleted(KeywordReport $keywordreport): void
    {
        //
    }
}
