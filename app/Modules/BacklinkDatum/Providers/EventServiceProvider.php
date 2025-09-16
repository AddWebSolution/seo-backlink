<?php

namespace App\Modules\BacklinkDatum\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumStoreEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumStoreEvent;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumUpdateEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumUpdateEvent;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumDeleteEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumDeleteEvent;
use App\Modules\BacklinkDatum\Events\AfterBacklinkDatumRestoreEvent;
use App\Modules\BacklinkDatum\Events\BeforeBacklinkDatumRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeBacklinkDatumStoreEvent::class => [],
        AfterBacklinkDatumStoreEvent::class => [],
        BeforeBacklinkDatumUpdateEvent::class => [],
        AfterBacklinkDatumUpdateEvent::class => [],
        BeforeBacklinkDatumDeleteEvent::class => [],
        AfterBacklinkDatumDeleteEvent::class => [],
        BeforeBacklinkDatumRestoreEvent::class => [],
		AfterBacklinkDatumRestoreEvent::class => [],
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