<x-layouts.app title="Crear candidato">
    <flux:heading size="xl" level="1" class="mb-6">Editar candidato</flux:heading>

    <form action="{{ route('rooms.candidates.update', [$room->id, $candidate->id]) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <flux:field>
                <flux:input label="DNI" name="identification" value="{{ $candidate->identification }}" disabled />
            </flux:field>
            <flux:field>
                <flux:input label="Nombre" name="name" value="{{ $candidate->name }}" disabled />
            </flux:field>
            <flux:field>
                <flux:label>Cargo</flux:label>
                <flux:description>Cambia solo si el candidato ya ha obtenido suficientes votos</flux:description>

                <select name="cargo_id" placeholder="Selecion el cargo..." class="px-3 py-2 border rounded-md">
                    <option value="" @selected(old('cargo_id', $candidate->cargo_id === null))>Sin cargo</option>
                    @foreach ($cargos as $cargo)
                        <option value="{{ $cargo->id }}" @selected(old('cargo_id', $cargo->id === $candidate->pivot->cargo_id))>{{ $cargo->name }}</option>
                    @endforeach
                </select>

                <flux:error name="cargo_id" />
            </flux:field>
        </div>

        <flux:button href="{{ route('events.show', $room->id) }}" class="mt-5">Cancelar</flux:button>
        <flux:button type="submit" variant="primary" class="mt-5">Actualizar candidato</flux:button>
    </form>
</x-layouts.app>
