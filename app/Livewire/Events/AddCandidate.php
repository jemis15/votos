<?php

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\User;
use Livewire\Component;

class AddCandidate extends Component
{
    public $search = '';
    public $users = [];
    public $event;
    public $userInEvent;

    function mount(Event $event)
    {
        $this->event = $event;

        $this->userInEvent = $this->event->candidates()->pluck('users.id')->toArray();
    }

    function updatedSearch($value)
    {
        $this->searchUser($value);
    }

    function searchUser($value)
    {
        if (strlen($value) > 1) { // evita buscar con 1 letra
            $this->users = User::where('name', 'like', "%{$value}%")
                ->orWhere('identification', 'like', "{$value}%")
                ->limit(10)
                ->get();
        } else {
            $this->users = [];
        }
    }

    function handleSelectUser($userId)
    {
        $existing = $this->event->users()->where('user_id', $userId)->first();

        if ($existing && in_array($existing->pivot->role_in_room, ['candidate', 'both'])) {
            // Si ya es candidate o both, no hacemos nada
            return;
        }

        if ($existing && $existing->pivot->role_in_room === 'voter') {
            // Si está como voter -> actualizar a both
            $this->event->users()->updateExistingPivot($userId, [
                'role_in_room' => 'both'
            ]);
        }

        if (!$existing) {
            // No existe en la sala -> agregar como candidato
            $this->event->users()->attach($userId, [
                'role_in_room' => 'candidate'
            ]);
        }

        $this->userInEvent[] = $userId;
    }

    public function render()
    {
        return view('livewire.events.add-candidate');
    }
}
