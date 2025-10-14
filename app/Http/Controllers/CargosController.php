<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Event;
use Illuminate\Http\Request;

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
            'name' => 'required|string|max:20|unique:App\Models\Cargo,name',
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
            'name' => 'required|string|max:20|unique:App\Models\Cargo,name,' . $id,
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
}
