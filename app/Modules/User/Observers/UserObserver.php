<?php

namespace App\Modules\User\Observers;

use App\Modules\User\Events\AfterUserStoreEvent;
use App\Modules\User\Events\BeforeUserStoreEvent;
use App\Modules\User\Events\AfterUserUpdateEvent;
use App\Modules\User\Events\BeforeUserUpdateEvent;
use App\Modules\User\Events\AfterUserDeleteEvent;
use App\Modules\User\Events\BeforeUserDeleteEvent;
use App\Modules\User\Events\AfterUserRestoreEvent;
use App\Modules\User\Events\BeforeUserRestoreEvent;
use App\Modules\User\Models\User;

class UserObserver
{
    /**
     * Handle the Item "creating" event.
     */
    public function creating(User $user) : void
    {
        BeforeUserStoreEvent::dispatch($user);
    }
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        AfterUserStoreEvent::dispatch($user);
    }


    /**
	 * Handle the User "updating" event.
	 */
	public function updating(User $user): void
	{
		BeforeUserUpdateEvent::dispatch($user);
	}

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        AfterUserUpdateEvent::dispatch($user);
    }


    /**
	 * Handle the User "deleting" event.
	 */
	public function deleting(User $user): void
	{
		BeforeUserDeleteEvent::dispatch($user);
	}

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        AfterUserDeleteEvent::dispatch($user);
    }

    /**
	 * Handle the User "restoring" event.
	 */
	public function restoring(User $user): void
	{
		BeforeUserRestoreEvent::dispatch($user);
	}

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        AfterUserRestoreEvent::dispatch($user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
