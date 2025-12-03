<div>
    <form wire:submit="createElection">
        <flux:field class="max-w-sm">
            <flux:label>Cargo</flux:label>
            <flux:select wire:model="cargo_id" placeholder="Escoje un cargo...">
                @foreach ($cargos as $cargo)
                    <flux:select.option value="{{ $cargo->id }}">{{ $cargo->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:error name="cargo_id" />
        </flux:field>

        <flux:field class="mt-6">
            <flux:label>Candidatos</flux:label>
            @foreach ($candidates as $candidate)
                <label class="flex items-center gap-2">
                    <flux:checkbox wire:model="candidatesSelected" value="{{ $candidate->id }}" />
                    <div class="flex items-center gap-x-2">
                        <flux:avatar src="{{ $candidate->image_url }}" name="{{ $candidate->name }}" initials:single />

                        <div>
                            <flux:heading size="lg">{{ $candidate->name }}</flux:heading>
                            <flux:text>DNI {{ $candidate->identification }}</flux:text>
                        </div>
                    </div>
                </label>
            @endforeach

            <flux:error name="candidatesSelected" />
        </flux:field>

        <div class="mt-6 space-x-2">
            <flux:button wire:navigate href="{{ route('events.show', $room->id) }}">Cancelar</flux:button>
            <flux:button type="submit" variant="primary">Crear eleccion</flux:button>
        </div>
    </form>
</div>
