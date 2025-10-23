<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Election;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CargosController extends Controller
{
    function create(Request $request)
    {
        $event = Event::findOrFail($request->event_id);

        return view('cargos.create', compact('event'));
    }

    function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('cargos', 'name')->where(function ($query) use ($request) {
                    $query->where('event_id', $request->event_id);
                })
            ],
            'event_id' => 'required|exists:App\Models\Event,id'
        ]);

        Cargo::create([
            'name' => $request->name,
            'event_id' => $request->event_id,
        ]);

        return redirect()->route('events.show', $request->event_id)->with('success', 'Cargo creado exitosamente.');
    }

    public function edit($id)
    {
        $cargo = Cargo::findOrFail($id);
        return view('cargos.edit', compact('cargo'));
    }

    public function update(Request $request, $id)
    {
        $cargo = Cargo::findOrFail($id);
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('cargos')->where(function ($query) use ($request) {
                    $query->where('event_id', $request->event_id)
                        ->orWhere('name', $request->name);
                })->ignore($request->event_id)
            ]
        ]);
        $cargo->name = $request->name;
        $cargo->save();
        return redirect()->route('events.show', $cargo->event_id)->with('success', 'Cargo actualizado correctamente.');
    }

    public function delete($id)
    {
        $cargo = Cargo::findOrFail($id);
        return view('cargos.delete', compact('cargo'));
    }

    public function destroy($id)
    {
        $cargo = Cargo::findOrFail($id);
        $event_id = $cargo->event_id;
        $cargo->delete();
        return redirect()->route('events.show', $event_id)->with('success', 'Cargo eliminado correctamente.');
    }

    function start(Cargo $cargo) {
        $election = new Election();
        $election->event_id = $cargo->event_id;
        $election->cargo_id = $cargo->id;
        $election->save();
        
        return redirect()->back()->with('success', 'Eleccion creado');
    }
}
