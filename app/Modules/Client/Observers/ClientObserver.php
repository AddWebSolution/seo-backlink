<?php

namespace App\Modules\Client\Observers;

use App\Modules\Client\Events\AfterClientStoreEvent;
use App\Modules\Client\Events\BeforeClientStoreEvent;
use App\Modules\Client\Events\AfterClientUpdateEvent;
use App\Modules\Client\Events\BeforeClientUpdateEvent;
use App\Modules\Client\Events\AfterClientDeleteEvent;
use App\Modules\Client\Events\BeforeClientDeleteEvent;
use App\Modules\Client\Events\AfterClientRestoreEvent;
use App\Modules\Client\Events\BeforeClientRestoreEvent;
use App\Modules\Client\Models\Client;

class ClientObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(Client $client) : void
    {
        BeforeClientStoreEvent::dispatch($client);
    }
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        AfterClientStoreEvent::dispatch($client);
    }


    /**
	 * Handle the Client "updating" event.
	 */
	public function updating(Client $client): void
	{
		BeforeClientUpdateEvent::dispatch($client);
	}

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        AfterClientUpdateEvent::dispatch($client);
    }


    /**
	 * Handle the Client "deleting" event.
	 */
	public function deleting(Client $client): void
	{
		BeforeClientDeleteEvent::dispatch($client);
	}

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        AfterClientDeleteEvent::dispatch($client);
    }

    /**
	 * Handle the Client "restoring" event.
	 */
	public function restoring(Client $client): void
	{
		BeforeClientRestoreEvent::dispatch($client);
	}

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        AfterClientRestoreEvent::dispatch($client);
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
