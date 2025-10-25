<?php

namespace App\Http\Controllers;

use App\Events\ElectionAnnulled;
use App\Events\ElectionToggleStatusEvent;
use App\Models\Cargo;
use App\Models\Election;
use App\Models\Event;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    function destroy(Election $election)
    {
        $election->delete();

        event(new ElectionAnnulled($election->event_id));

        return redirect()->back()->with('success', 'Elecccion eliminado con exito');
    }

    function toggleStatus(Election $election)
    {
        $election->status = $election->status === 'closed' || $election->status === 'created' ? 'open' : 'closed';
        $election->save();

        // if ($election->status === 'closed') {
        //     $event = Event::find($election->event_id);

        //     return $event->voters()->count();
        // }

        event(new ElectionToggleStatusEvent($election->event_id, $election));

        return redirect()->back()->with('success', $election->status === 'open' ? 'Elecccion inicado' : 'Eleccion cerrado');
    }
}
