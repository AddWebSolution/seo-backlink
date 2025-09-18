<?php

namespace App\Modules\KeywordDatum\Observers;

use App\Modules\KeywordDatum\Events\AfterKeywordDatumStoreEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumStoreEvent;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumUpdateEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumUpdateEvent;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumDeleteEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumDeleteEvent;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumRestoreEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumRestoreEvent;
use App\Modules\KeywordDatum\Models\KeywordDatum;

class KeywordDatumObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(KeywordDatum $keyworddatum) : void
    {
        BeforeKeywordDatumStoreEvent::dispatch($keyworddatum);
    }
    /**
     * Handle the KeywordDatum "created" event.
     */
    public function created(KeywordDatum $keyworddatum): void
    {
        AfterKeywordDatumStoreEvent::dispatch($keyworddatum);
    }


    /**
	 * Handle the KeywordDatum "updating" event.
	 */
	public function updating(KeywordDatum $keyworddatum): void
	{
		BeforeKeywordDatumUpdateEvent::dispatch($keyworddatum);
	}

    /**
     * Handle the KeywordDatum "updated" event.
     */
    public function updated(KeywordDatum $keyworddatum): void
    {
        AfterKeywordDatumUpdateEvent::dispatch($keyworddatum);
    }


    /**
	 * Handle the KeywordDatum "deleting" event.
	 */
	public function deleting(KeywordDatum $keyworddatum): void
	{
		BeforeKeywordDatumDeleteEvent::dispatch($keyworddatum);
	}

    /**
     * Handle the KeywordDatum "deleted" event.
     */
    public function deleted(KeywordDatum $keyworddatum): void
    {
        AfterKeywordDatumDeleteEvent::dispatch($keyworddatum);
    }

    /**
	 * Handle the KeywordDatum "restoring" event.
	 */
	public function restoring(KeywordDatum $keyworddatum): void
	{
		BeforeKeywordDatumRestoreEvent::dispatch($keyworddatum);
	}

    /**
     * Handle the KeywordDatum "restored" event.
     */
    public function restored(KeywordDatum $keyworddatum): void
    {
        AfterKeywordDatumRestoreEvent::dispatch($keyworddatum);
    }

    /**
     * Handle the KeywordDatum "force deleted" event.
     */
    public function forceDeleted(KeywordDatum $keyworddatum): void
    {
        //
    }
}
