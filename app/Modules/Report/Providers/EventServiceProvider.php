<?php

namespace App\Modules\Report\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\Report\Events\AfterReportStoreEvent;
use App\Modules\Report\Events\BeforeReportStoreEvent;
use App\Modules\Report\Events\AfterReportUpdateEvent;
use App\Modules\Report\Events\BeforeReportUpdateEvent;
use App\Modules\Report\Events\AfterReportDeleteEvent;
use App\Modules\Report\Events\BeforeReportDeleteEvent;
use App\Modules\Report\Events\AfterReportRestoreEvent;
use App\Modules\Report\Events\BeforeReportRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeReportStoreEvent::class => [],
        AfterReportStoreEvent::class => [],
        BeforeReportUpdateEvent::class => [],
        AfterReportUpdateEvent::class => [],
        BeforeReportDeleteEvent::class => [],
        AfterReportDeleteEvent::class => [],
        BeforeReportRestoreEvent::class => [],
		AfterReportRestoreEvent::class => [],
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