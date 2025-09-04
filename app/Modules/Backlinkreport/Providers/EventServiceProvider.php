<?php

namespace App\Modules\Backlinkreport\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportStoreEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportStoreEvent;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportUpdateEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportUpdateEvent;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportDeleteEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportDeleteEvent;
use App\Modules\Backlinkreport\Events\AfterBacklinkreportRestoreEvent;
use App\Modules\Backlinkreport\Events\BeforeBacklinkreportRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeBacklinkreportStoreEvent::class => [],
        AfterBacklinkreportStoreEvent::class => [],
        BeforeBacklinkreportUpdateEvent::class => [],
        AfterBacklinkreportUpdateEvent::class => [],
        BeforeBacklinkreportDeleteEvent::class => [],
        AfterBacklinkreportDeleteEvent::class => [],
        BeforeBacklinkreportRestoreEvent::class => [],
		AfterBacklinkreportRestoreEvent::class => [],
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