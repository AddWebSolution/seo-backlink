<?php

namespace App\Modules\BacklinkHistory\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryStoreEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryStoreEvent;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryUpdateEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryUpdateEvent;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryDeleteEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryDeleteEvent;
use App\Modules\BacklinkHistory\Events\AfterBacklinkHistoryRestoreEvent;
use App\Modules\BacklinkHistory\Events\BeforeBacklinkHistoryRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeBacklinkHistoryStoreEvent::class => [],
        AfterBacklinkHistoryStoreEvent::class => [],
        BeforeBacklinkHistoryUpdateEvent::class => [],
        AfterBacklinkHistoryUpdateEvent::class => [],
        BeforeBacklinkHistoryDeleteEvent::class => [],
        AfterBacklinkHistoryDeleteEvent::class => [],
        BeforeBacklinkHistoryRestoreEvent::class => [],
		AfterBacklinkHistoryRestoreEvent::class => [],
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