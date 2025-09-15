<?php

namespace App\Modules\KeywordReport\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\KeywordReport\Events\AfterKeywordReportStoreEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportStoreEvent;
use App\Modules\KeywordReport\Events\AfterKeywordReportUpdateEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportUpdateEvent;
use App\Modules\KeywordReport\Events\AfterKeywordReportDeleteEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportDeleteEvent;
use App\Modules\KeywordReport\Events\AfterKeywordReportRestoreEvent;
use App\Modules\KeywordReport\Events\BeforeKeywordReportRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeKeywordReportStoreEvent::class => [],
        AfterKeywordReportStoreEvent::class => [],
        BeforeKeywordReportUpdateEvent::class => [],
        AfterKeywordReportUpdateEvent::class => [],
        BeforeKeywordReportDeleteEvent::class => [],
        AfterKeywordReportDeleteEvent::class => [],
        BeforeKeywordReportRestoreEvent::class => [],
		AfterKeywordReportRestoreEvent::class => [],
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