<x-layouts.app title="Editar usuario">
    <h1 class="text-xl mb-4">Editar usuario</h1>
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-3">
            <flux:input label="Nombre" name="name" value="{{ old('name', $user->name) }}" />
            <flux:input label="Documento (DNI)" name="identification" value="{{ old('identification', $user->identification) }}" />
            <flux:input label="Correo" name="email" value="{{ old('email', $user->email) }}" />
        </div>

        <flux:button class="mt-5" href="{{ route('users') }}">Cancelar</flux:button>
        <flux:button class="mt-5" variant="primary" type="submit">Actualizar</flux:button>
    </form>
</x-layouts.app>
