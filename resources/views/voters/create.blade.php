<x-layouts.app title="Crear evento">
    <flux:button href="{{ route('events.show', $event->id) }}" icon="arrow-left" class="mb-4">Atras</flux:button>

    <h1 class="text-xl mb-4">Agregar usuarios a la sala <b>{{ $event->name }}</b></h1>

    <livewire:events.candidates.search-user :event="$event" />
</x-layouts.app>
