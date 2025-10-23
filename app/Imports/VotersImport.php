<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\User;
use App\Models\Voter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithValidation;

class VotersImport implements ToCollection
{
    public $event;

    function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $dnis = $rows->pluck(0)->filter()->unique()->toArray();

        $usuarios = User::whereIn('identification', $dnis)->pluck('id')->toArray();

        $this->event->voters()->sync($usuarios);
    }
}
