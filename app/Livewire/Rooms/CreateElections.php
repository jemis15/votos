<?php

namespace App\Livewire\Rooms;

use App\Events\ElectionCreated;
use App\Events\RoomUpdated;
use App\Models\Cargo;
use App\Models\Election;
use App\Models\Event;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateElections extends Component
{
    public Event $room;

    public $candidates = [];
    public $cargos = [];
    public $count = 0;

    #[Validate(['required', 'exists:cargos,id'])]
    public string $cargo_id = '';

    #[Validate(['required', 'array', 'min:2'])]
    public array $candidatesSelected = [];

    public function mount(Event $room)
    {
        $this->cargos = $room->cargos;
        $this->candidates = $room->candidates()
            ->wherePivotNull('cargo_id')
            ->get();
    }

    function createElection()
    {
        $this->validate();

        $election = new Election();
        $election->event_id = $this->room->id;
        $election->cargo_id = $this->cargo_id;
        $election->save();

        $election->candidates()->attach($this->candidatesSelected);

        $data = [
            'type' => 'election-created',
            'election' => $election,
            'candidates' => $this->candidatesSelected,
        ];

        broadcast(new RoomUpdated($this->room->id, $data));

        session()->flash('success', 'Eleccion creado con exito.');

        $this->redirect(route('events.show', $this->room->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.rooms.create-elections');
    }
}
