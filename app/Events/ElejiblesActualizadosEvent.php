<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElejiblesActualizadosEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $eventId;
    public $eligibles;

    /**
     * Create a new event instance.
     */
    public function __construct($eventId, $eligibles)
    {
        $this->eventId = $eventId;
        $this->eligibles = $eligibles;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('events.' . $this->eventId),
        ];
    }

    // public function broadcastAs()
    // {
    //     return 'elegibles.actualizados';
    // }
}
