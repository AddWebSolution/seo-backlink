<?php

namespace App\Modules\User\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\User\Events\AfterUserStoreEvent;
use App\Modules\User\Events\BeforeUserStoreEvent;
use App\Modules\User\Events\AfterUserUpdateEvent;
use App\Modules\User\Events\BeforeUserUpdateEvent;
use App\Modules\User\Events\AfterUserDeleteEvent;
use App\Modules\User\Events\BeforeUserDeleteEvent;
use App\Modules\User\Events\AfterUserRestoreEvent;
use App\Modules\User\Events\BeforeUserRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeUserStoreEvent::class => [],
        AfterUserStoreEvent::class => [],
        BeforeUserUpdateEvent::class => [],
        AfterUserUpdateEvent::class => [],
        BeforeUserDeleteEvent::class => [],
        AfterUserDeleteEvent::class => [],
        BeforeUserRestoreEvent::class => [],
		AfterUserRestoreEvent::class => [],
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