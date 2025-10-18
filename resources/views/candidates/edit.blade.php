<x-layouts.app title="Crear candidato">
    <h1 class="text-xl mb-4">Editar candidato</h1>
    <form action="{{ route('candidates.update', $candidate->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <flux:field>
            <flux:label>DNI</flux:label>

            <flux:input name="identification" value="{{ old('identification', $candidate->identification ?? '') }}" />

            <flux:error name="identification" />
        </flux:field>
        <flux:field>
            <flux:label>Nombre</flux:label>

            <flux:input name="name" value="{{ old('name', $candidate->name ?? '') }}" />

            <flux:error name="name" />
        </flux:field>
        <flux:field class="mt-5">
            <flux:label>Foto</flux:label>

            <flux:input name="photo_url" type="file" />

            <flux:error name="photo_url" />

            @if (isset($candidate) && $candidate->photo_url)
                <img src="{{ $candidate->photo_url }}" alt="Foto actual" width="100">
            @endif
        </flux:field>

        <flux:button href="{{ route('events.show', $event->id) }}" class="mt-5">Cancelar</flux:button>
        <flux:button type="submit" variant="primary" class="mt-5">Actualizar candidato</flux:button>
    </form>
</x-layouts.app>
