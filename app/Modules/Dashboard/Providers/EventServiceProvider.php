<?php

namespace App\Modules\Dashboard\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\Dashboard\Events\AfterDashboardStoreEvent;
use App\Modules\Dashboard\Events\BeforeDashboardStoreEvent;
use App\Modules\Dashboard\Events\AfterDashboardUpdateEvent;
use App\Modules\Dashboard\Events\BeforeDashboardUpdateEvent;
use App\Modules\Dashboard\Events\AfterDashboardDeleteEvent;
use App\Modules\Dashboard\Events\BeforeDashboardDeleteEvent;
use App\Modules\Dashboard\Events\AfterDashboardRestoreEvent;
use App\Modules\Dashboard\Events\BeforeDashboardRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeDashboardStoreEvent::class => [],
        AfterDashboardStoreEvent::class => [],
        BeforeDashboardUpdateEvent::class => [],
        AfterDashboardUpdateEvent::class => [],
        BeforeDashboardDeleteEvent::class => [],
        AfterDashboardDeleteEvent::class => [],
        BeforeDashboardRestoreEvent::class => [],
		AfterDashboardRestoreEvent::class => [],
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