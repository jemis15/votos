<?php

namespace App\Http\Controllers;

use App\Events\EventOpen;
use App\Events\UpdateEvent;
use App\Models\Candidate;
use App\Models\Cargo;
use App\Models\Election;
use App\Models\Eligible;
use App\Models\Event;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            },
            'elections.candidates'
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

        broadcast(new UpdateEvent($event));

        return redirect()->back()->with('success', $event->is_open ? 'Evento abiento' : 'Evento cerrado');
    }

    function live(Event $event)
    {
        $event->load([
            'cargos',
            'elections',
        ]);

        $event->users = User::join('room_user', 'users.id', 'room_user.user_id')
            ->select('users.id', 'users.name', 'users.identification', 'room_user.role_in_room', 'cargo_id', 'profile_photo_path')
            ->get();

        $event->votes = Vote::whereIn('election_id', $event->elections->pluck('id'))
            ->selectRaw('votes.voter_id, votes.election_id, votes.candidate_id')
            ->get();

        $eligibles = Eligible::whereIn('election_id', $event->elections->pluck('id'))->get();
        // $votes = Vote::whereIn('election_id', $event->elections->pluck('id'))
        //     ->groupBy('election_id')
        //     ->groupBy('candidate_id')
        //     ->selectRaw('count(voter_id) as votes, candidate_id, election_id')
        //     ->get();

        $event->elections->each(function ($election) use ($eligibles) {
            $election->candidates = $eligibles->where('election_id', $election->id)->pluck('candidate_id');
            // $election->votes = $votes->where('election_id', $election->id)->keyBy('');
        });

        $event->voters = $event->users
            ->whereIn('role_in_room', ['voter', 'both'])
            ->pluck('id');

        $event->candidates = $event->users
            ->whereIn('role_in_room', ['candidate', 'both'])
            ->pluck('id');

        $event->winners = $event->users
            ->whereNotNull('pivot.cargo_id')
            ->pluck('id');

        $currentElectionId = $event->elections->where('status', 'open')->first()?->id;

        return view('events.public-live', compact('event', 'currentElectionId'));

        return $event->name;
    }
}
