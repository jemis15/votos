<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Event;
use Illuminate\Http\Request;

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
            'voters'
        ]);

        return view('events.show', [
            'event' => $event,
            'cargos' => $event->cargos,
            'candidates' => $event->candidates,
            'voters' => $event->voters
        ]);
    }
}
