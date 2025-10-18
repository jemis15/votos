<?php

namespace App\Http\Controllers;

use App\Imports\CandidatesImport;
use App\Models\Candidate;
use App\Models\Cargo;
use App\Models\Event;
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

    public function edit(Candidate $candidate)
    {
        $event = $candidate->event;

        return view('candidates.edit', compact('candidate', 'event'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'identification' => 'required|numeric|digits:8',
            'photo_url' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo_url')) {
            // Elimina la foto anterior si existe
            if ($candidate->photo_url) {
                $oldPath = str_replace('/storage/', '', $candidate->photo_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('photo_url')->store('candidates', 'public');
            $candidate->photo_url = Storage::url($path);
        }

        $candidate->name = $request->name;
        $candidate->save();

        return redirect()
            ->route('events.show', $candidate->event_id)
            ->with('success', 'Candidato actualizado correctamente.');
    }

    public function delete(Candidate $candidate)
    {
        return view('candidates.delete', compact('candidate'));
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->photo_url) {
            $oldPath = str_replace('/storage/', '', $candidate->photo_url);
            Storage::disk('public')->delete($oldPath);
        }
        $candidate->delete();
        return redirect()->route('events.show', $candidate->event_id)->with('success', 'Candidato eliminado correctamente.');
    }

    function import() {
        // \Maatwebsite\Excel\Facades\Excel::import(new CandidatesImport, )
    }
}
