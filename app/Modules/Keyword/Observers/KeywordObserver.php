<?php

namespace App\Modules\Keyword\Observers;

use App\Modules\Keyword\Events\AfterKeywordStoreEvent;
use App\Modules\Keyword\Events\BeforeKeywordStoreEvent;
use App\Modules\Keyword\Events\AfterKeywordUpdateEvent;
use App\Modules\Keyword\Events\BeforeKeywordUpdateEvent;
use App\Modules\Keyword\Events\AfterKeywordDeleteEvent;
use App\Modules\Keyword\Events\BeforeKeywordDeleteEvent;
use App\Modules\Keyword\Events\AfterKeywordRestoreEvent;
use App\Modules\Keyword\Events\BeforeKeywordRestoreEvent;
use App\Modules\Keyword\Models\Keyword;

class KeywordObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(Keyword $keyword) : void
    {
        BeforeKeywordStoreEvent::dispatch($keyword);
    }
    /**
     * Handle the Keyword "created" event.
     */
    public function created(Keyword $keyword): void
    {
        AfterKeywordStoreEvent::dispatch($keyword);
    }


    /**
	 * Handle the Keyword "updating" event.
	 */
	public function updating(Keyword $keyword): void
	{
		BeforeKeywordUpdateEvent::dispatch($keyword);
	}

    /**
     * Handle the Keyword "updated" event.
     */
    public function updated(Keyword $keyword): void
    {
        AfterKeywordUpdateEvent::dispatch($keyword);
    }


    /**
	 * Handle the Keyword "deleting" event.
	 */
	public function deleting(Keyword $keyword): void
	{
		BeforeKeywordDeleteEvent::dispatch($keyword);
	}

    /**
     * Handle the Keyword "deleted" event.
     */
    public function deleted(Keyword $keyword): void
    {
        AfterKeywordDeleteEvent::dispatch($keyword);
    }

    /**
	 * Handle the Keyword "restoring" event.
	 */
	public function restoring(Keyword $keyword): void
	{
		BeforeKeywordRestoreEvent::dispatch($keyword);
	}

    /**
     * Handle the Keyword "restored" event.
     */
    public function restored(Keyword $keyword): void
    {
        AfterKeywordRestoreEvent::dispatch($keyword);
    }

    /**
     * Handle the Keyword "force deleted" event.
     */
    public function forceDeleted(Keyword $keyword): void
    {
        //
    }
}
