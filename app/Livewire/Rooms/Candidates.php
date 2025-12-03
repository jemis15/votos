<?php

namespace App\Livewire\Rooms;

use App\Models\User;
use Livewire\Component;

class Candidates extends Component
{
    public string $roomId;
    public $candidates;
    public $event;

    public function mount()
    {
        $this->candidates = User::join('room_user', 'users.id', 'room_user.user_id')
            ->where('room_user.room_id', $this->roomId)
            ->whereIn('room_user.role_in_room', ['candidate', 'both'])
            ->select('users.*')
            ->get();
    }

    public function render()
    {
        return view('livewire.rooms.candidates');
    }
}
