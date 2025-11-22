<?php

namespace App\Http\Controllers;

use App\Events\ElectionAnnulled;
use App\Events\ElectionCreated;
use App\Events\ElectionToggleCandidateEvent;
use App\Events\ElectionToggleStatusEvent;
use App\Models\Candidate;
use App\Models\Cargo;
use App\Models\Election;
use App\Models\Event;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'cargo_id' => 'required|exists:cargos,id',
        ]);

        // Verificar si ya existe una eleccion en curso para el evento
        $activeExists = Election::where('event_id', $request->event_id)
            ->where('status', '!=', 'closed')
            ->exists();

        if ($activeExists) {
            return redirect()->back()->with('error', 'Ya existe una eleccion activa en este evento.');
        }

        $election = Election::create([
            'event_id' => $request->event_id,
            'cargo_id' => $request->cargo_id,
        ]);

        broadcast(new ElectionCreated($request->event_id, $election));

        return redirect()->back()->with('success', 'Eleccion creada con exito');
    }

    function destroy(Election $election)
    {
        $election->delete();

        broadcast(new ElectionAnnulled($election->event_id, $election->id));

        return redirect()->back()->with('success', 'Elecccion eliminado con exito');
    }

    function toggleStatus(Election $election)
    {
        $electionsInProgress = Election::where('event_id', $election->event_id)
            ->where('status', '!=', 'closed')
            ->where('id', '!=', $election->id)
            ->exists();

        if ($electionsInProgress) {
            return redirect()->back()->with('error', 'No se puede abrir la eleccion. Ya existe otra eleccion activa en este evento.');
        }

        if (!$election->hasMinimumActiveCandidates(2)) {
            return redirect()->back()->with('error', 'No se puede abrir la eleccion. Se requieren al menos 2 candidatos.');
        }

        $election->status = $election->status === 'closed' || $election->status === 'created' ? 'open' : 'closed';
        $election->save();

        broadcast(new ElectionToggleStatusEvent($election->event_id, $election->load('candidates')));

        return redirect()->back()->with('success', $election->status === 'open' ? 'Elecccion inicado' : 'Eleccion cerrado');
    }

    function toggleCandidate(Request $request, Election $election)
    {
        $request->validate([
            'candidate_id' => 'required|exists:users,id',
        ]);

        $changes = $election->candidates()->toggle($request->candidate_id);

        broadcast(new ElectionToggleCandidateEvent(
            $election->event_id,
            $election->id,
            $changes
        ));

        return redirect()->back()->with('success', 'Candidato ' . (count($changes['attached']) ? 'agregado' : 'removido') . ' exitosamente.');
    }
}
