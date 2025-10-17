<?php

namespace App\Modules\ClientDomain\Events;

use App\Modules\ClientDomain\Listeners\SendClientDomainToN8nListener;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Modules\ClientDomain\Models\ClientDomain;

class AfterClientDomainStoreEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ClientDomain $clientDomain)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new SendClientDomainToN8nListener,
        ];
    }
}
