<x-layouts.app title="Editar evento">
    <h1 class="text-xl mb-4">Editar evento</h1>
    <form action="{{ route('events.update', $event->id) }}" method="POST" class="flex flex-col gap-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block mb-1 font-medium">Nombre del evento</label>
            <input type="text" name="name" id="name" value="{{ old('name', $event->name) }}" class="w-full border border-gray-300 rounded px-3 py-2" required>
        </div>
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Actualizar evento</button>
        </div>
    </form>
</x-layouts.app>
