<?php

namespace App\Livewire\Rooms\Elections;

use App\Models\Event;
use Livewire\Component;

class Index extends Component
{
    public $event;

    public function mount(Event $room) {
        $this->event = $room;
    }

    public function render(Event $event)
    {
        return view('livewire.rooms.elections.index');
    }
}
