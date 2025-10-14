<x-layouts.app title="Eliminar cargo">
    <h1 class="text-xl mb-4">Eliminar cargo</h1>
    <div class="mb-4">¿Estás seguro que deseas eliminar el cargo <strong>{{ $cargo->name }}</strong>?</div>
    <form action="{{ route('cargos.destroy', $cargo->id) }}" method="POST" class="flex gap-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Eliminar</button>
        <a href="{{ route('events.show', $cargo->event_id) }}" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancelar</a>
    </form>
</x-layouts.app>
