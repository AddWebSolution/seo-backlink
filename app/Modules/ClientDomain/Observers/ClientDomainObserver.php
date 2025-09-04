<?php

namespace App\Modules\ClientDomain\Observers;

use App\Modules\ClientDomain\Events\AfterClientDomainStoreEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainStoreEvent;
use App\Modules\ClientDomain\Events\AfterClientDomainUpdateEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainUpdateEvent;
use App\Modules\ClientDomain\Events\AfterClientDomainDeleteEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainDeleteEvent;
use App\Modules\ClientDomain\Events\AfterClientDomainRestoreEvent;
use App\Modules\ClientDomain\Events\BeforeClientDomainRestoreEvent;
use App\Modules\ClientDomain\Models\ClientDomain;

class ClientDomainObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(ClientDomain $clientdomain): void
    {
        BeforeClientDomainStoreEvent::dispatch($clientdomain);
    }
    /**
     * Handle the ClientDomain "created" event.
     */
    public function created(ClientDomain $clientdomain): void
    {
        AfterClientDomainStoreEvent::dispatch($clientdomain);
    }


    /**
     * Handle the ClientDomain "updating" event.
     */
    public function updating(ClientDomain $clientdomain): void
    {
        BeforeClientDomainUpdateEvent::dispatch($clientdomain);
    }

    /**
     * Handle the ClientDomain "updated" event.
     */
    public function updated(ClientDomain $clientdomain): void
    {
        AfterClientDomainUpdateEvent::dispatch($clientdomain);
    }


    /**
     * Handle the ClientDomain "deleting" event.
     */
    public function deleting(ClientDomain $clientdomain): void
    {
        BeforeClientDomainDeleteEvent::dispatch($clientdomain);
    }

    /**
     * Handle the ClientDomain "deleted" event.
     */
    public function deleted(ClientDomain $clientdomain): void
    {
        AfterClientDomainDeleteEvent::dispatch($clientdomain);
    }

    /**
     * Handle the ClientDomain "restoring" event.
     */
    public function restoring(ClientDomain $clientdomain): void
    {
        BeforeClientDomainRestoreEvent::dispatch($clientdomain);
    }

    /**
     * Handle the ClientDomain "restored" event.
     */
    public function restored(ClientDomain $clientdomain): void
    {
        AfterClientDomainRestoreEvent::dispatch($clientdomain);
    }

    /**
     * Handle the ClientDomain "force deleted" event.
     */
    public function forceDeleted(ClientDomain $clientdomain): void
    {
        //
    }
}
