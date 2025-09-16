<?php

namespace App\Modules\KeywordDatum\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumStoreEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumStoreEvent;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumUpdateEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumUpdateEvent;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumDeleteEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumDeleteEvent;
use App\Modules\KeywordDatum\Events\AfterKeywordDatumRestoreEvent;
use App\Modules\KeywordDatum\Events\BeforeKeywordDatumRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeKeywordDatumStoreEvent::class => [],
        AfterKeywordDatumStoreEvent::class => [],
        BeforeKeywordDatumUpdateEvent::class => [],
        AfterKeywordDatumUpdateEvent::class => [],
        BeforeKeywordDatumDeleteEvent::class => [],
        AfterKeywordDatumDeleteEvent::class => [],
        BeforeKeywordDatumRestoreEvent::class => [],
		AfterKeywordDatumRestoreEvent::class => [],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}