<?php

namespace App\Modules\RivalDomain\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\RivalDomain\Listeners\SendRivalDomainToN8nListener;
use App\Modules\RivalDomain\Events\AfterRivalDomainStoreEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainStoreEvent;
use App\Modules\RivalDomain\Events\AfterRivalDomainUpdateEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainUpdateEvent;
use App\Modules\RivalDomain\Events\AfterRivalDomainDeleteEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainDeleteEvent;
use App\Modules\RivalDomain\Events\AfterRivalDomainRestoreEvent;
use App\Modules\RivalDomain\Events\BeforeRivalDomainRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeRivalDomainStoreEvent::class => [],
        AfterRivalDomainStoreEvent::class => [
            SendRivalDomainToN8nListener::class
        ],
        BeforeRivalDomainUpdateEvent::class => [],
        AfterRivalDomainUpdateEvent::class => [],
        BeforeRivalDomainDeleteEvent::class => [],
        AfterRivalDomainDeleteEvent::class => [],
        BeforeRivalDomainRestoreEvent::class => [],
		AfterRivalDomainRestoreEvent::class => [],
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