<x-layouts.app title="Crear candidato">
    <flux:button href="{{ route('events.show', $event->id) }}" icon="arrow-left" class="mb-4">Atras</flux:button>

    <h1 class="text-xl mb-4">Agregar candidatos a la sala <b>{{ $event->name }}</b></h1>

    <livewire:events.add-candidate :event="$event" />
</x-layouts.app>
