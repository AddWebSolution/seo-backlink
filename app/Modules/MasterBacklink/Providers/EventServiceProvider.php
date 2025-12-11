<?php

namespace App\Modules\MasterBacklink\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkStoreEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkStoreEvent;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkUpdateEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkUpdateEvent;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkDeleteEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkDeleteEvent;
use App\Modules\MasterBacklink\Events\AfterMasterBacklinkRestoreEvent;
use App\Modules\MasterBacklink\Events\BeforeMasterBacklinkRestoreEvent;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BeforeMasterBacklinkStoreEvent::class => [],
        AfterMasterBacklinkStoreEvent::class => [],
        BeforeMasterBacklinkUpdateEvent::class => [],
        AfterMasterBacklinkUpdateEvent::class => [],
        BeforeMasterBacklinkDeleteEvent::class => [],
        AfterMasterBacklinkDeleteEvent::class => [],
        BeforeMasterBacklinkRestoreEvent::class => [],
		AfterMasterBacklinkRestoreEvent::class => [],
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