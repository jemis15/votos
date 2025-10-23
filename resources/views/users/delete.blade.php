<x-layouts.app title="Eliminar usuario">
    <h1 class="text-xl mb-4">Eliminar usuario</h1>
    <div>¿Seguro que deseas eliminar a <b>{{ $user->name }}</b>?</div>
    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="mt-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Eliminar</button>
        <a href="{{ route('users') }}" class="bg-gray-300 px-4 py-2 rounded">Cancelar</a>
    </form>
</x-layouts.app>
