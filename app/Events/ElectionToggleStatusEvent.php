<?php

namespace App\Events;

use App\Models\Cargo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElectionToggleStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $eventId;
    public $election;

    /**
     * Create a new event instance.
     */
    public function __construct($eventId, $election)
    {
        $this->eventId = $eventId;
        $this->election = $election;
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

    public function broadcastWith()
    {
        return [
            'election' => $this->election,
            'cargo' => Cargo::find($this->election->cargo_id),
        ];
    }
}
