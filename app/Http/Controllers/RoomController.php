<?php

namespace App\Http\Controllers;

use App\Events\RegisteredVote;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Eligible;
use App\Models\Event;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    function index(Request $request, Event $room)
    {
        $exists = $room->voters()->where('user_id', $request->user()->id)->exists();

        // verificamos que el usuario pertenesca a la sala
        if (!$exists) {
            return abort(404);
        }

        $room->load([
            'cargos',
            'candidates',
            'elections'
        ]);

        $eligibles = Eligible::whereIn('election_id', $room->elections->pluck('id'))->get();

        $room->elections->each(function ($election) use ($eligibles) {
            $election->candidates = $eligibles->where('election_id', $election->id)->pluck('candidate_id');
        });

        $myVotes = Vote::where('voter_id', $request->user()->id)->get();
        $currentElectionId = $room->elections->where('status', 'open')->first()?->id;

        return view('events.live', compact('room', 'myVotes', 'currentElectionId'));

        return $votes;

        $election = $room->elections()
            ->where('status', 'open')
            ->join('cargos', 'elections.cargo_id', 'cargos.id')
            ->select('elections.*', 'cargos.name as cargo')
            // ->with('candidates')
            ->first();

        $eligibles = $election?->candidates ?? [];

        $myVote = null;
        if ($election) {
            $myVote = Vote::where('voter_id', Auth::id())
                ->where('election_id', $election->id)
                ->first();
        }

        $winners = [];

        if (!$room->is_open) {
            $winners = $room->candidates()->whereNotNull('cargo_id')->get();
        }

        $summaryVotes = $election?->votes()->groupBy('candidate_id')->count();
        // Vote::join('users', 'votes.candidate_id', 'users.id')
        //     ->join('elections', 'votes.election_id', 'elections.id')
        //     ->where('elections.event_id', $room->id)
        //     ->groupBy('candidates.id')
        //     ->select(
        //         'candidates.id as candidate_id',
        //         DB::raw('count(votes.candidate_id) as votes')
        //     )
        //     ->get();

        return $room;

        return view('events.live', compact('room', 'election', 'eligibles', 'myVote', 'summaryVotes', 'winners'));
    }

    function registerVote(Request $request)
    {
        $electionId = $request->electionId;
        $candidateId = $request->candidateId;

        $election = Election::findOrFail($electionId);

        // verificamos que la sala estea abierta
        $event = Event::findOrFail($election->event_id);

        if (!$event->is_open) {
            return response()->json([
                'message' => 'La sala se encuentra cerrada.',
                'success' => false
            ]);
        }

        // verificamos si esta autorizado a votar
        $autorized = $event->voters()->where('user_id', $request->user()->id)->exists();

        if (!$autorized) {
            return response()->json([
                'message' => 'No perteneses a la sala.',
                'success' => false
            ]);
        }

        // verificamos si registro su voto anteriormente
        $exists = Vote::where('voter_id', $request->user()->id)
            ->where('election_id', $electionId)
            ->exists();

        if ($exists) return response()->json([
            'message' => 'Tu voto ya fue registrado.',
            'success' => false
        ]);

        $vote = new Vote();
        $vote->voter_id = $request->user()->id;
        $vote->election_id = $electionId;
        $vote->candidate_id = $candidateId;
        $vote->save();

        event(new RegisteredVote($election->event_id, $vote->toArray()));

        return response()->json([
            'success' => true,
            'message' => 'Voto registrado'
        ]);
    }

    function report(Event $room, Request $request)
    {
        $currentElectionId = $request->query('election_id');

        $elections = $room->elections()
            // ->where('status', 'closed')
            ->join('cargos', 'elections.cargo_id', 'cargos.id')
            ->select('elections.*', 'cargos.name as cargo')
            ->get();

        $votes = Vote::join('elections', 'votes.election_id', 'elections.id')
            ->where('elections.event_id', $room->id)
            ->where('elections.id', $currentElectionId)
            ->select('votes.*')
            ->get();


        // return $votes;

        return view('rooms.report', compact('room', 'votes', 'currentElectionId', 'elections'));
    }
}
