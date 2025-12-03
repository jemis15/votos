<?php

namespace App\Http\Controllers;

use App\Events\ElejiblesActualizadosEvent;
use App\Events\UpdateCandidateCargo;
use App\Imports\CandidatesImport;
use App\Models\Candidate;
use App\Models\Cargo;
use App\Models\Eligible;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function create(Request $request)
    {
        $event = Event::findOrFail($request->event_id);

        return view('candidates.create', compact('event'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'identification' => 'required|numeric|digits:8',
            'photo_url' => 'nullable|image|max:2048',
            'event_id' => 'required|exists:events,id',
        ]);

        $path = null;
        if ($request->hasFile('photo_url')) {
            $path = $request->file('photo_url')->store('candidates', 'public');
        }

        Candidate::create([
            'name' => $request->name,
            'identification' => $request->identification,
            'photo_url' => $path ? Storage::url($path) : null,
            'cargo_id' => $request->cargo_id,
            'event_id' => $request->event_id,
        ]);

        return redirect()
            ->route('events.show', $request->event_id)
            ->with('success', 'Candidato creado correctamente.');
    }

    public function show(Candidate $candidate)
    {
        return view('candidates.show', compact('candidate'));
    }

    public function edit(Event $room)
    {
        $candidate = $room->candidates()
            ->where('users.id', request()->route('candidate'))
            ->firstOrFail();

        // Cargos que ya estan asignados excluyendo el del candidato actual
        $cargosFilled = $room->candidates()
            ->wherePivotNotNull('cargo_id')
            ->get(['users.id'])
            ->filter(fn ($c) => $c->pivot->cargo_id !== $candidate->pivot->cargo_id)
            ->pluck('pivot.cargo_id');

        // Cargos disponibles para asignar
        $cargos = $room->cargos()->whereNotIn('id', $cargosFilled)->get();

        return view('candidates.edit', compact('room', 'candidate', 'cargos'));
    }

    public function update(Request $request, Event $room)
    {
        $request->validate([
            'cargo_id' => 'nullable|exists:cargos,id',
        ]);

        $room->users()->updateExistingPivot(request()->route('candidate'), [
            'cargo_id' => $request->cargo_id,
        ]);

        // event(new UpdateCandidateCargo($candidate->event_id, $candidate));

        return redirect()
            ->route('events.show', $room->id)
            ->with('success', 'Candidato actualizado correctamente.');
    }

    public function delete(Candidate $candidate)
    {
        return view('candidates.delete', compact('candidate'));
    }

    public function destroy(Event $event, string $candidateId)
    {
        $candidate = $event->users()->findOrFail($candidateId);

        $existsInElection = Eligible::join('elections', 'eligibles.election_id', 'elections.id')
            ->where('eligibles.candidate_id', $candidateId)
            ->where('elections.event_id', $event->id)
            ->exists();

        if ($existsInElection) {
            // Si existe en una eleccion, no hacemos nada
            return redirect()->back()->with('error', 'El candidato pertenece a una eleccion.');
        }

        if ($candidate->pivot->role_in_room === 'both') {
            // Si está como both -> actualizar a voter
            $event->users()->updateExistingPivot($candidateId, [
                'role_in_room' => 'voter'
            ]);
        } else {
            $event->users()->detach($candidate->id);
        }

        return redirect()->back()->with('success', 'Candidato quitado correctamente.');
    }

    function import(Request $request, Event $event)
    {
        $request->validate([
            'file_candidates' => 'required|file|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel'
        ], [], [], 'import');

        \Maatwebsite\Excel\Facades\Excel::import(new CandidatesImport($event), $request->file_candidates);
        return redirect()->back()->with('success', 'Importado con exito !!');
    }

    function toggleElegible(Candidate $candidate)
    {
        $candidate->eligible = !$candidate->eligible;
        $candidate->save();

        $eligiblesActivos = Candidate::where('event_id', $candidate->event_id)
            ->where('eligible', true)
            ->get();

        event(new ElejiblesActualizadosEvent($candidate->event_id, $eligiblesActivos));

        return redirect()->back()->with('success', 'Elegible');
    }
}
