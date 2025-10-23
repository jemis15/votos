<?php

namespace App\Livewire\Events\Candidates;

use App\Models\Event;
use App\Models\User;
use App\Models\Voter;
use Livewire\Component;

class SearchUser extends Component
{
    public $search = '';
    public $users = [];
    public $event;
    public $userInEvent;

    function mount(Event $event)
    {
        $this->event = $event;

        $this->userInEvent = $this->event->voters()->pluck('users.id')->toArray();
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
        if (!$this->event->voters()->where('user_id', $userId)->exists()) {
            $this->event->voters()->attach($userId);
            $this->userInEvent[] = $userId;
        }
    }

    public function render()
    {
        return view('livewire.events.candidates.search-user');
    }
}
