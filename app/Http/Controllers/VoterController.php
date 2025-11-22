<?php

namespace App\Http\Controllers;

use App\Imports\VotersImport;
use App\Models\Event;
use App\Models\Voter;
use Illuminate\Http\Request;

class VoterController extends Controller
{
    function create(Request $request) {
        $event = Event::findOrFail($request->event_id);

        return view('voters.create', compact('event'));
    }

    function destroy(Event $event, string $userId) {
        $voter = $event->users()->findOrFail($userId);

        if ($voter->pivot->role_in_room === 'both') {
            // Si está como both -> actualizar a candidate
            $event->users()->updateExistingPivot($userId, [
                'role_in_room' => 'candidate'
            ]);
        }else {
            $event->users()->detach($userId);
        }

        return redirect()->route('events.show', $event->id)->with('success', 'Eliminado con exito!');
    }

    function import(Request $request, Event $event)
    {
        $request->validate([
            'file_voters' => 'required|file|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel'
        ], [], [], 'import');

        \Maatwebsite\Excel\Facades\Excel::import(new VotersImport($event), $request->file_voters);
        return redirect()->back()->with('success', 'Importado con exito !!');
    }
}
