<?php

namespace App\Modules\RivalDomain\Events;

use App\Modules\RivalDomain\Listeners\SendRivalDomainToN8nListener;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Modules\RivalDomain\Models\RivalDomain;

class AfterRivalDomainStoreEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public RivalDomain $rivalDomain)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new SendRivalDomainToN8nListener
        ];
    }
}
