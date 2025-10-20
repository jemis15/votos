<?php

namespace App\Imports;

use App\Models\Candidate;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;

class CandidatesImport implements ToModel, WithValidation
{
    public $eventId;

    function __construct(Event $event)
    {
        $this->eventId = $event->id;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Candidate([
            'identification' => $row[0],
            'name' => $row[1],
            'event_id' => $this->eventId
        ]);
    }

    function rules(): array
    {
        return [
            '0' => [
                "required",
                "numeric",
                "digits:8",
                Rule::unique('candidates', 'identification')
                    ->where(function ($query) {
                        $query->where('event_id', $this->eventId);
                    })
            ],
            '2' => "required|string|max:100"
        ];
    }
}
