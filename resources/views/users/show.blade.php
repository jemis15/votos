<x-layouts.app title="Detalle usuario">
    <h1 class="text-xl mb-4">Detalle de {{ $user->name }}</h1>
    <div>
        <b>Email:</b> {{ $user->email }}<br>
    </div>
    <a href="{{ route('users.edit', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded">Editar</a>
    <a href="{{ route('users.delete', $user->id) }}" class="bg-red-600 text-white px-4 py-2 rounded">Eliminar</a>
    <a href="{{ route('users.index') }}" class="bg-gray-300 px-4 py-2 rounded">Volver</a>
</x-layouts.app>
