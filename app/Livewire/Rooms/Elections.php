<?php

namespace App\Livewire\Rooms;

use App\Models\Event;
use Livewire\Component;

class Elections extends Component
{
    public function render()
    {
        $event = Event::findOrFail(1);
        return view('livewire.rooms.elections', [
            'event' => $event,
        ]);
    }
}
