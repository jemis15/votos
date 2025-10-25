<?php

namespace App\Http\Controllers;

use App\Events\RegisteredVote;
use App\Models\Candidate;
use App\Models\Election;
use App\Models\Event;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    function index(Request $request, string $roomId)
    {
        $exists = Voter::where('event_id', $roomId)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (!$exists) {
            return abort(404);
        }

        $event = Event::findOrFail($roomId);

        $election = Election::join('cargos', 'elections.cargo_id', 'cargos.id')
            ->where('elections.event_id', $event->id)
            ->where('status', 'open')
            ->select('elections.*', 'cargos.name as cargo')
            ->first();

        $eligibles = Candidate::where('event_id', $event->id)
            ->where('eligible', true)
            ->get();

        $voteById = null;
        if ($election) {
            $voteById = Vote::where('user_id', auth()->id())
                ->where('election_id', $election->id)
                ->first()
                ?->candidate_id;
        }

        $winners = [];

        if (!$event->is_open) {
            $winners = Candidate::with('cargo')
                ->withCount('votes')
                ->where('event_id', $event->id)
                ->whereNotNull('cargo_id')
                ->get();
        }

        $summaryVotes = Vote::join('candidates', 'votes.candidate_id', 'candidates.id')
            ->where('candidates.event_id', $event->id)
            ->groupBy('candidates.id')
            ->select(
                'candidates.id as candidate_id',
                DB::raw('count(votes.candidate_id) as votes')
            )
            ->get();


        return view('events.live', compact('event', 'election', 'eligibles', 'voteById', 'summaryVotes', 'winners'));
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
        $autorized = Voter::where('event_id', $election->event_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (!$autorized) {
            return response()->json([
                'message' => 'No perteneses a la sala.',
                'success' => false
            ]);
        }

        // verificamos si registro su voto anteriormente
        $exists = Vote::where('user_id', auth()->id())
            ->where('election_id', $electionId)
            ->exists();

        if ($exists) return response()->json([
            'message' => 'Tu voto ya fue registrado.',
            'success' => false
        ]);

        $vote = new Vote();
        $vote->user_id = $request->user()->id;
        $vote->election_id = $electionId;
        $vote->candidate_id = $candidateId;
        $vote->save();

        event(new RegisteredVote($election->event_id, $vote->toArray()));

        return response()->json([
            'success' => true,
            'message' => 'Voto registrado'
        ]);
    }
}
