<?php

namespace App\Modules\ClientDomain\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\ClientDomain\Events\AfterClientDomainStoreEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainStoreEvent;
use App\Modules\ClientDomain\Events\AfterClientDomainUpdateEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainUpdateEvent;
use App\Modules\ClientDomain\Events\AfterClientDomainDeleteEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainDeleteEvent;
use App\Modules\ClientDomain\Events\AfterClientDomainRestoreEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeClientDomainStoreEvent::class => [],
        AfterClientDomainStoreEvent::class => [],
        BeforeClientDomainUpdateEvent::class => [],
        AfterClientDomainUpdateEvent::class => [],
        BeforeClientDomainDeleteEvent::class => [],
        AfterClientDomainDeleteEvent::class => [],
        BeforeClientDomainRestoreEvent::class => [],
		AfterClientDomainRestoreEvent::class => [],
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