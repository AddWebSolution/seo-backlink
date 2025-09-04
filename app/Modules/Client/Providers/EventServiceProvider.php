<?php

namespace App\Modules\Client\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\Client\Events\AfterClientStoreEvent;
use App\Modules\Client\Events\BeforeClientStoreEvent;
use App\Modules\Client\Events\AfterClientUpdateEvent;
use App\Modules\Client\Events\BeforeClientUpdateEvent;
use App\Modules\Client\Events\AfterClientDeleteEvent;
use App\Modules\Client\Events\BeforeClientDeleteEvent;
use App\Modules\Client\Events\AfterClientRestoreEvent;
use App\Modules\Client\Events\BeforeClientRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeClientStoreEvent::class => [],
        AfterClientStoreEvent::class => [],
        BeforeClientUpdateEvent::class => [],
        AfterClientUpdateEvent::class => [],
        BeforeClientDeleteEvent::class => [],
        AfterClientDeleteEvent::class => [],
        BeforeClientRestoreEvent::class => [],
        AfterClientRestoreEvent::class => [],
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
