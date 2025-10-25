<?php

namespace App\Http\Controllers;

use App\Events\UpdateEvent;
use App\Models\Candidate;
use App\Models\Cargo;
use App\Models\Election;
use App\Models\Event;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Can;

use function Laravel\Prompts\select;

class EventController extends Controller
{
    function index()
    {
        $events = Event::all();

        return view('events.index', compact('events'));
    }

    function create()
    {
        return view('events.create');
    }

    function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:20',
        ]);

        Event::create([
            'name' => $request->name,
        ]);

        return redirect()->route('events.index')->with('success', 'Evento creado exitosamente.');
    }

    function edit($id)
    {
        $event = Event::findOrFail($id);
        return view('events.edit', compact('event'));
    }

    function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:20',
        ]);
        $event->name = $request->name;
        $event->save();
        return redirect()->route('events.index')->with('success', 'Evento actualizado correctamente.');
    }

    function delete($id)
    {
        $event = Event::findOrFail($id);
        return view('events.delete', compact('event'));
    }

    function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Evento eliminado correctamente.');
    }

    function show(Event $event)
    {
        $event->load([
            'candidates',
            'cargos',
            'voters',
            'elections' => function ($query) {
                $query->join('cargos', 'elections.cargo_id', 'cargos.id')
                    ->select('elections.*', 'cargos.name as cargo');
            }
        ]);

        return view('events.show', [
            'event' => $event,
            'cargos' => $event->cargos,
            'candidates' => $event->candidates,
            'voters' => $event->voters
        ]);
    }

    function toggleStatus(Event $event)
    {
        $event->is_open = !$event->is_open;
        $event->save();

        // broadcast(new UpdateEvent($event));
        event(new UpdateEvent($event));

        return redirect()->back()->with('success', $event->is_open ? 'Evento abiento' : 'Evento cerrado');
    }

    function live(Event $event)
    {
        $event->load(
            'voters',
            'cargos',
            'candidates'
        );

        $winners = $event->candidates()->whereNotNull('cargo_id')->get();

        $eligibles = [];
        $election = null;
        $votes = [];

        if ($event->is_open) {
            $eligibles = $event->candidates()->where('eligible', true)->get();

            $election = Election::join('cargos', 'elections.cargo_id', 'cargos.id')
                ->whereIn('status', ['open', 'closed'])
                ->select('elections.*', 'cargos.name as cargo')
                ->orderBy('id', 'desc')
                ->first();
        }

        if ($election) {
            $votes = Vote::where('election_id', $election->id)->get();
        }


        $summaryVotes = Vote::join('candidates', 'votes.candidate_id', 'candidates.id')
            ->where('candidates.event_id', $event->id)
            ->groupBy('candidates.id')
            ->select(
                'candidates.id as candidate_id',
                DB::raw('count(votes.candidate_id) as votes')
            )
            ->get();


        return view('events.public-live', compact(
            'event',
            'eligibles',
            'election',
            'winners',
            'summaryVotes',
            'votes'
        ));
    }
}
