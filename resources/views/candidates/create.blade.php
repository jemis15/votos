<x-layouts.app title="Crear candidato">
    <h1 class="text-xl mb-4">Crear candidato</h1>
    <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <flux:field>
            <flux:label>DNI</flux:label>

            <flux:input name="identification" value="{{ old('identification') }}" />

            <flux:error name="identification" />
        </flux:field>
        <flux:field class="mt-5">
            <flux:label>Nombre</flux:label>

            <flux:input name="name" value="{{ old('name') }}" />

            <flux:error name="name" />
        </flux:field>
        <flux:field class="mt-5">
            <flux:label>Foto</flux:label>

            <flux:input name="photo_url" type="file" />

            <flux:error name="photo_url" />
        </flux:field>

        <input type="hidden" name="event_id" value="{{ $event->id }}">

        <flux:button href="{{ route('events.show', $event->id) }}" class="mt-5">Cancelar</flux:button>
        <flux:button type="submit" variant="primary" class="mt-5">Crear candidato</flux:button>
    </form>
</x-layouts.app>
